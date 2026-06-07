<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("supplier.index");
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
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'shop_name' => 'nullable|required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors(),]);
        }

        $filePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('suppliers', $fileName, 'public');
        }

        Supplier::create([
            'name' => $request->name,
            'image' => $filePath,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'shop_name' => $request->shop_name,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Supplier Added Successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function fetchAll()
    {
        $suppliers = Supplier::latest()->get();
        $output = '
        <table class="table table-bordered table-striped align-middle"
            id="supplierTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Image</th>
                    <th>Phone</th>
                    <th>Shop Name</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';
        // CHECK DATA
        if ($suppliers->count() > 0) {
            foreach ($suppliers as $supplier) {
                $output .= '
                <tr>
                    <td>' . $supplier->id . '</td>
                    <td>' . $supplier->name . '</td>
                    <td>' . $supplier->email . '</td>
                    <td>' . ($supplier->image ? '<img src="' . asset('storage/' . $supplier->image) . '"
                           width="30"
                           height="30"
                           class="rounded-circle img-thumbnail">' : '<span class="badge bg-secondary">No Image</span>') . '
                    </td>
                    <td>' . $supplier->phone . '</td>
                    <td>' . $supplier->shop_name . '</td>
                    <td>' . $supplier->address . '</td>
                    <td>
                        <!-- EDIT -->
                        <button
                            class="btn btn-success btn-sm editIcon"
                            id="' . $supplier->id . '">
                            <i class="bi-pencil-square"></i>
                        </button>
                        <!-- DELETE -->
                        <button
                            class="btn btn-danger btn-sm deleteIcon"
                            id="' . $supplier->id . '">
                            <i class="bi-trash"></i>
                        </button>
                    </td>
                </tr>';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="8">
                    <h5 class="text-danger text-center">
                        No Suppliers Found!
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
    public function edit(Supplier $supplier)
    {
        return response()->json(
            Supplier::findOrFail(request()->id)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $supplier = Supplier::findOrFail(
            $request->supplier_id
        );

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone'   => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        if ($request->hasFile('image')) {

            if (
                !empty($supplier->image) &&
                Storage::disk('public')->exists($supplier->image)
            ) {
                Storage::disk('public')->delete(
                    $supplier->image
                );
            }

            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $supplier->image = $file->storeAs('suppliers', $fileName, 'public');
        }

        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->shop_name = $request->shop_name;

        $supplier->save();

        return response()->json([
            'status' => 200,
            'message' => 'Supplier Updated Successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Supplier::findOrFail(request()->id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Supplier Deleted Successfully',
        ]);
    }

    // fetch trash data

    public function fetchTrash()
    {
        $suppliers = Supplier::onlyTrashed()->latest()->get();
        $output = '
        <table class="table table-bordered table-striped
             align-middle" id="trashTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Image</th>
                    <th>Phone</th>
                    <th>Shop Name</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';

        // CHECK TRASH DATA
        if ($suppliers->count() > 0) {
            foreach ($suppliers as $supplier) {
                $output .= '
                <tr>
                    <td>' . $supplier->id . '</td>
                    <td>' . $supplier->name . '</td>
                    <td>' . $supplier->email . '</td>
                    <td>
                        <img
                            src="' . asset('storage/' . $supplier->image) . '"
                            height="30"
                            width="30"
                            class="rounded-circle img-thumbnail">
                    </td>
                    <td>' . $supplier->phone . '</td>
                    <td>' . $supplier->shop_name . '</td>
                    <td>' . $supplier->address . '</td>

                    <td>
                        <!-- RESTORE -->
                        <button
                            class="btn btn-success btn-sm restoreIcon"
                            id="' . $supplier->id . '">
                            <i class="bi-arrow-clockwise"></i>
                        </button>

                        <!-- PERMANENT DELETE -->
                        <button
                            class="btn btn-danger btn-sm permanentDeleteIcon"
                            id="' . $supplier->id . '">
                            <i class="bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
                ';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="8">
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
        Supplier::onlyTrashed()->findOrFail($request->id)->restore();
        return response()->json([
            'status' => 200,
            'message' => 'Supplier Restored Successfully',
        ]);
    }

    public function permanentDelete(Request $request)
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($request->id);
        if (
            !empty($supplier->image) &&
            Storage::disk('public')->exists($supplier->image)
        ) {
            Storage::disk('public')->delete(
                $supplier->image
            );
        }

        $supplier->forceDelete();

        return response()->json([
            'status' => 200,
            'message' => 'Supplier Permanently Deleted',
        ]);
    }
}
