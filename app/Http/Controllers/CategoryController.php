<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * INDEX PAGE
     */
    public function index()
    {
        return view('category.index');
    }

    /**
     * STORE CATEGORY
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,inactive',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors(),]);
        }

        $filePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs(
                'categories',
                $fileName,
                'public'
            );
        }

        // INSERT DATA
        Category::create([
            'name' => $request->name,
            'image' => $filePath,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Category Added Successfully',
        ]);
    }

    /**
     * FETCH ACTIVE CATEGORIES
     */
    public function fetchAll()
    {
        $categories = Category::latest()->get();
        $output = '
        <table class="table table-bordered table-striped align-middle"
            id="categoryTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';
        // CHECK DATA

        $i = 1;
        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                // STATUS BADGE
                $badge = $category->status == 'active'
                    ? 'bg-success'
                    : 'bg-danger';
                $output .= '
                <tr>
                      <td>' . $i++ . '</td>
                    <td>' . $category->name . '</td>
                    <td>
                       ' . (
                    $category->image
                    ? '<img src="' . asset('storage/' . $category->image) . '"
                width="30"
                height="30"
                class="rounded-circle img-thumbnail">'
                    : '<span class="badge bg-secondary">No Image</span>'
                ) . '
                    </td>
                    <td>
                        <span class="badge ' . $badge . '">
                            ' . ucfirst($category->status) . '
                        </span>
                    </td>

                    <td>
                        <!-- EDIT -->
                        <button
                            class="btn btn-success btn-sm editIcon"
                            id="' . $category->id . '">
                            <i class="bi-pencil-square"></i>
                        </button>

                        <!-- DELETE -->
                        <button
                            class="btn btn-danger btn-sm deleteIcon"
                            id="' . $category->id . '">
                            <i class="bi-trash"></i>
                        </button>
                    </td>
                </tr>
                ';
            }
        } else {

            $output .= '
            <tr>
                <td colspan="5">
                    <h5 class="text-danger text-center">
                        No Categories Found!
                    </h5>
                </td>
            </tr>
            ';
        }

        $output .= '</tbody></table>';
        return $output;
    }

    /**
     * FETCH TRASH CATEGORIES
     */
    public function fetchTrash()
    {
        $categories = Category::onlyTrashed()->latest()->get();

        $output = '

        <table class="table table-bordered table-striped
             align-middle"
            id="trashTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';

        // CHECK TRASH DATA
        if ($categories->count() > 0) {

            foreach ($categories as $category) {
                $output .= '
                <tr>
                    <td>' . $category->id . '</td>
                    <td>' . $category->name . '</td>
                    <td>
                       ' . (
                    $category->image
                    ? '<img src="' . asset('storage/' . $category->image) . '"
                height="30"
                width="30"
                class="rounded-circle img-thumbnail">'
                    : '<span class="badge bg-secondary">No Image</span>'
                ) . '
                    </td>

                    <td>
                        <span class="badge bg-danger">
                            Deleted
                        </span>
                    </td>

                    <td>
                        <!-- RESTORE -->
                        <button
                            class="btn btn-success btn-sm restoreIcon"
                            id="' . $category->id . '">
                            <i class="bi-arrow-clockwise"></i>
                        </button>

                        <!-- PERMANENT DELETE -->
                        <button
                            class="btn btn-danger btn-sm permanentDeleteIcon"
                            id="' . $category->id . '">
                            <i class="bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
                ';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="5">
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

    /**
     * EDIT CATEGORY
     */
    public function edit(Request $request)
    {
        $id = $request->id;
        $category = Category::find($id);
        return response()->json($category);
    }

    /**
     * UPDATE CATEGORY
     */
    public function update(Request $request)
    {
        // FIND CATEGORY
        $category = Category::find($request->cat_id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        // VALIDATION FAILED
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        // IMAGE UPDATE
        if ($request->hasFile('image')) {
            // DELETE OLD IMAGE
            if (
                !empty($category->image) && Storage::disk('public')->exists($category->image)
            ) {
                Storage::disk('public')->delete($category->image);
            }
            // NEW IMAGE
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('categories', $fileName, 'public');
            $category->image = $filePath;
        }

        // UPDATE DATA
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();
        return response()->json(['status' => 200, 'message' => 'Category Updated Successfully',]);
    }

    /**
     * SOFT DELETE
     */
    public function destroy(Request $request)
    {
        $id = $request->id;

        $category = Category::find($id);

        $category->delete();

        return response()->json([

            'status' => 200,

            'message' => 'Category Moved To Trash',

        ]);
    }

    /**
     * RESTORE CATEGORY
     */
    public function restore(Request $request)
    {
        $id = $request->id;

        Category::onlyTrashed()
            ->find($id)
            ->restore();

        return response()->json([

            'status' => 200,

            'message' => 'Category Restored Successfully',

        ]);
    }

    /**
     * PERMANENT DELETE
     */
    public function permanentDelete(Request $request)
    {
        $id = $request->id;

        $category = Category::onlyTrashed()
            ->find($id);

        // DELETE IMAGE
        if (

            !empty($category->image)

            && Storage::disk('public')->exists($category->image)

        ) {

            Storage::disk('public')
                ->delete($category->image);
        }

        // FORCE DELETE
        $category->forceDelete();

        return response()->json([

            'status' => 200,

            'message' => 'Category Permanently Deleted',

        ]);
    }
}
