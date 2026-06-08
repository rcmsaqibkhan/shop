<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $customers     = Customer::latest()->get();
        $products      = Product::latest()->get();
        $orders        = Order::with('customer')->get();
        $trashedOrders = Order::with('customer')->onlyTrashed()->latest()->get();
        $lastOrder     = Order::latest()->first();
        $orderNumber   = $lastOrder
            ? 'ORD-' . str_pad($lastOrder->id + 1, 3, '0', STR_PAD_LEFT)
            : 'ORD-00001';

        return view('order.index', compact(
            'customers',
            'products',
            'orders',
            'trashedOrders',
            'orderNumber'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'order_number'  => 'required|unique:orders,order_number',
            'date'          => 'required',
            'product_ids'   => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'price'         => 'required|array',
            'quantity'      => 'required|array',
            'total_amount'  => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'customer_id'  => $request->customer_id,
                'order_number' => $request->order_number,
                'date'         => $request->date,
                'discount'     => $request->discount ?? 0,
                'total_amount' => $request->total_amount,
            ]);

            foreach ($request->product_ids as $key => $productId) {
                $product = Product::findOrFail($productId);

                if ($product->quantity < $request->quantity[$key]) {
                    throw new \Exception($product->name . ' ka stock available nahi hai.');
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $request->quantity[$key],
                    'price'      => $request->price[$key],
                ]);

                $product->quantity -= $request->quantity[$key];
                $product->save();
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order ban gaya!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function editData($id)
    {
        $order    = Order::with('orderItems')->findOrFail($id);
        $products = Product::latest()->get();

        return response()->json([
            'order'    => $order,
            'items'    => $order->orderItems,
            'products' => $products,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'date'          => 'required',
            'product_ids'   => 'required|array|min:1',
            'product_ids.*' => 'required|exists:products,id',
            'price'         => 'required|array',
            'quantity'      => 'required|array',
            'total_amount'  => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            foreach ($order->orderItems as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->quantity += $oldItem->quantity;
                    $product->save();
                }
            }

            $order->update([
                'customer_id'  => $request->customer_id,
                'date'         => $request->date,
                'discount'     => $request->discount ?? 0,
                'total_amount' => $request->total_amount,
            ]);

            $order->orderItems()->delete();

            foreach ($request->product_ids as $key => $productId) {
                $product = Product::findOrFail($productId);

                if ($product->quantity < $request->quantity[$key]) {
                    throw new \Exception($product->name . ' ka stock available nahi hai.');
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $request->quantity[$key],
                    'price'      => $request->price[$key],
                ]);

                $product->quantity -= $request->quantity[$key];
                $product->save();
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order update ho gaya!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['success' => true, 'message' => 'Order delete ho gaya!']);
    }

    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();
        return response()->json(['success' => true, 'message' => 'Order restore ho gaya!']);
    }

    public function forceDelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->forceDelete();
        return response()->json(['success' => true, 'message' => 'Order permanently delete ho gaya!']);
    }

    public function invoice($id)
    {
        $order = Order::with(['customer', 'orderItems.product'])->findOrFail($id);
        return response()->json(['order' => $order]);
    }
}
