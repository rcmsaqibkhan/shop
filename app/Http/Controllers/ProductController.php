<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $product = Product::all();
        $trashPro = Product::onlyTrashed()->get();
        return view('product.index', compact('categories', 'suppliers', 'product', 'trashPro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'code' => 'nullable|string|unique:products,code',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'buy_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        $file = $request->file('image');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('products', $fileName, 'public');

        Product::create([
            'name' => $request->name,
            'image' => $filePath,
            'category_id' => $request->category_id,
            'code' => $request->code,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'buy_date' => $request->buy_date,
            'supplier_id' => $request->supplier_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Product Added Successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function fetchAll()
    {

        $products = Product::latest()->get();
        $output = '
        <table class="table table-bordered table-striped align-middle"
            id="productsTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Code</th>
                    <th>Buy Price</th>
                    <th>Sell Price</th>
                    <th>Buy Date</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';

        // CHECK DATA
        if ($products->count() > 0) {
            foreach ($products as $product) {
                $output .= '
                <tr>
                    <td>' . $product->id . '</td>
                    <td>' . $product->name . '</td>
                    <td>' . $product->category->name . '</td>
                    <td>
                        <img src="' . asset('storage/' . $product->image) . '"
                            width="30" height="30" class="rounded-circle">
                    </td>
                    <td>' . $product->code . '</td>
                    <td>' . $product->buy_price . '</td>
                    <td>' . $product->sell_price . '</td>
                    <td>' . date('d-M-Y', strtotime($product->buy_date)) . '</td>
                    <td >
                    <span class="badge bg-success">
                        ' . $product->quantity . '
                  </span>
                  </td>
                    <td>
                        <!-- EDIT -->
                        <button
                            class="btn btn-success btn-sm editIcon"
                            id="' . $product->id . '">
                            <i class="bi-pencil-square"></i>
                        </button>
                        <!-- DELETE -->
                        <button
                            class="btn btn-danger btn-sm deleteIcon"
                            id="' . $product->id . '">
                            <i class="bi-trash"></i>
                        </button>
                    </td>
                </tr>';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="10">
                    <h5 class="text-danger text-center">
                        No Products Found!
                    </h5>
                </td>
            </tr>
            ';
        }

        $output .= '</tbody></table>';
        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        return response()->json(Product::findOrFail($request->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'code' => 'nullable|string|unique:products,code,' . $product->id,
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'nullable|numeric|min:0',
            'buy_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        if ($request->hasFile('image')) {
            if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $product->image = $file->storeAs('products', $fileName, 'public');
        }

        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->code = $request->code;
        $product->buy_price = $request->buy_price;
        $product->sell_price = $request->sell_price;
        $product->buy_date = $request->buy_date;
        $product->supplier_id = $request->supplier_id;
        $product->quantity = $request->quantity;

        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product Updated Successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Product::findOrFail($request->id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product Deleted Successfully',
        ]);
    }

    public function fetchTrash()
    {
        $products = Product::onlyTrashed()->latest()->get();
        $output = '
        <table class="table table-bordered table-striped
             align-middle" id="trashTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Code</th>
                    <th>Buy Price</th>
                    <th>Sell Price</th>
                    <th>Buy Date</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';

        // CHECK TRASH DATA
        if ($products->count() > 0) {
            foreach ($products as $product) {
                $output .= '
                <tr>
                    <td>' . $product->id . '</td>
                    <td>' . $product->name . '</td>
                    <td>' . $product->category->name . '</td>
                    <td>
                        <img
                            src="' . asset('storage/' . $product->image) . '"
                            height="30"
                            width="30"
                            class="rounded-circle img-thumbnail">
                    </td>
                    <td>' . $product->code . '</td>
                    <td>' . $product->buy_price . '</td>
                    <td>' . $product->sell_price . '</td>
                    <td>' . $product->buy_date . '</td>
                    <td>' . $product->quantity . '</td>

                    <td>
                        <!-- RESTORE -->
                        <button
                            class="btn btn-success btn-sm restoreIcon"
                            id="' . $product->id . '">
                            <i class="bi-arrow-clockwise"></i>
                        </button>

                        <!-- PERMANENT DELETE -->
                        <button
                            class="btn btn-danger btn-sm permanentDeleteIcon"
                            id="' . $product->id . '">
                            <i class="bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
                ';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="10">
                    <h5 class="text-danger text-center">
                        Trash Empty!
                    </h5>
                </td>
            </tr>
            ';
        }
        $output .= '</tbody></table>';
        return $output;
    }

    public function restore(Request $request)
    {
        Product::onlyTrashed()->findOrFail($request->id)->restore();
        return response()->json([
            'status' => 200,
            'message' => 'Product Restored Successfully',
        ]);
    }

    public function permanentDelete(Request $request)
    {
        $product = Product::onlyTrashed()->findOrFail($request->id);
        if (
            !empty($product->image) &&
            Storage::disk('public')->exists($product->image)
        ) {
            Storage::disk('public')->delete(
                $product->image
            );
        }

        $product->forceDelete();

        return response()->json([
            'status' => 200,
            'message' => 'Product Permanently Deleted',
        ]);
    }
}
