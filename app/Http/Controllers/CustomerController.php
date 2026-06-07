<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.index');
    }

    public function store(Request $request)
    {
        // VALIDATION
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',


        ]);


        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors(),]);
        }

        $filePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('customers', $fileName, 'public');
        }


        // INSERT DATA
        Customer::create([
            'name' => $request->name,
            'image' => $filePath,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Customer Added Successfully',
        ]);
    }

    public function fetchAll()
    {

        $customers = Customer::latest()->get();
        $output = '
        <table class="table table-bordered table-striped align-middle"
            id="customerTable">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Image</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';

        // CHECK DATA
        if ($customers->count() > 0) {
            foreach ($customers as $customer) {
                $output .= '
                <tr>
                    <td>' . $customer->id . '</td>
                    <td>' . $customer->name . '</td>
                    <td>' . $customer->email . '</td>
                    <td>' . ($customer->image ? '<img src="' . asset('storage/' . $customer->image) . '"
                           width="30"
                           height="30"
                           class="rounded-circle img-thumbnail">' : '<span class="badge bg-secondary">No Image</span>') . '
                    </td>
                    <td>  ' . $customer->phone . '</td>
                    <td>' . $customer->address . '</td>
                    <td>
                        <!-- EDIT -->
                        <button
                            class="btn btn-success btn-sm editIcon"
                            id="' . $customer->id . '">
                            <i class="bi-pencil-square"></i>
                        </button>
                        <!-- DELETE -->
                        <button
                            class="btn btn-danger btn-sm deleteIcon"
                            id="' . $customer->id . '">
                            <i class="bi-trash"></i>
                        </button>
                    </td>
                </tr>';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="7">
                    <h5 class="text-danger text-center">
                        No Customers Found!
                    </h5>
                </td>
            </tr>
            ';
        }

        $output .= '</tbody></table>';
        return $output;
    }

    public function edit(Request $request)
    {
        return response()->json(Customer::findOrFail($request->id));
    }

    public function update(Request $request)
    {
        $customer = Customer::findOrFail(
            $request->customer_id
        );

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone'   => 'required|string|max:20',
            'address' => 'required|string|max:255',
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
                !empty($customer->image) &&
                Storage::disk('public')->exists($customer->image)
            ) {
                Storage::disk('public')->delete(
                    $customer->image
                );
            }

            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $customer->image = $file->storeAs('customers', $fileName, 'public');
        }

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;

        $customer->save();

        return response()->json([
            'status' => 200,
            'message' => 'Customer Updated Successfully',
        ]);
    }

    public function destroy(Request $request)
    {
        Customer::findOrFail($request->id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Customer Moved To Trash',
        ]);
    }

    public function fetchTrash()
    {
        $customers = Customer::onlyTrashed()->latest()->get();
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
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';

        // CHECK TRASH DATA
        if ($customers->count() > 0) {
            foreach ($customers as $customer) {
                $output .= '
                <tr>
                    <td>' . $customer->id . '</td>
                    <td>' . $customer->name . '</td>
                    <td>' . $customer->email . '</td>
                    <td>' . ($customer->image ? '<img src="' . asset('storage/' . $customer->image) . '"
                           width="30"
                           height="30"
                           class="rounded-circle img-thumbnail">' : '<span class="badge bg-secondary">No Image</span>') . '
                    </td>
                    <td>' . $customer->phone . '</td>
                    <td>' . $customer->address . '</td>

                    <td>
                        <!-- RESTORE -->
                        <button
                            class="btn btn-success btn-sm restoreIcon"
                            id="' . $customer->id . '">
                            <i class="bi-arrow-clockwise"></i>
                        </button>

                        <!-- PERMANENT DELETE -->
                        <button
                            class="btn btn-danger btn-sm permanentDeleteIcon"
                            id="' . $customer->id . '">
                            <i class="bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
                ';
            }
        } else {
            $output .= '
            <tr>
                <td colspan="7">
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
        Customer::onlyTrashed()->findOrFail($request->id)->restore();
        return response()->json([
            'status' => 200,
            'message' => 'Customer Restored Successfully',
        ]);
    }

    public function permanentDelete(Request $request)
    {
        $customer = Customer::onlyTrashed()->findOrFail($request->id);
        if (
            !empty($customer->image) &&
            Storage::disk('public')->exists($customer->image)
        ) {
            Storage::disk('public')->delete(
                $customer->image
            );
        }

        $customer->forceDelete();

        return response()->json([
            'status' => 200,
            'message' => 'Customer Permanently Deleted',
        ]);
    }
}
