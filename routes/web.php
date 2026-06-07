<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $totalProducts = Product::count();
        $orders = Order::with('customer')->latest()->get();
        $todaySale = Order::whereDate('created_at', Carbon::today())->sum('total_amount');
        $stock = Product::sum('quantity');
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        return view('dashboard', compact('categories', 'suppliers', 'totalProducts', 'todayOrders', 'todaySale', 'stock', 'orders'));
    })->name('dashboard');
    Route::get('/tables', function () {
        return view('tables.table');
    })->name('tables');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/fetch-all', [CategoryController::class, 'fetchAll'])->name('categories.fetchAll');
    Route::get('/categories/fetch-trash', [CategoryController::class, 'fetchTrash'])->name('categories.fetchTrash');
    Route::get('/categories/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/update', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/delete', [CategoryController::class, 'destroy'])->name('categories.delete');
    Route::post('/categories/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::post('/categories/permanent-delete', [CategoryController::class, 'permanentDelete'])->name('categories.permanentDelete');

    // Customer routes
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/fetch-all', [CustomerController::class, 'fetchAll'])->name('customers.fetchAll');
        Route::get('/fetch-trash', [CustomerController::class, 'fetchTrash'])->name('customers.fetchTrash');
        Route::post('/store', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::post('/update', [CustomerController::class, 'update'])->name('customers.update');
        Route::post('/delete', [CustomerController::class, 'destroy'])->name('customers.delete');
        Route::post('/restore', [CustomerController::class, 'restore'])->name('customers.restore');
        Route::post('/permanent-delete', [CustomerController::class, 'permanentDelete'])->name('customers.permanentDelete');
    });

    // Supplier routes
    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/fetch-all', [SupplierController::class, 'fetchAll'])->name('suppliers.fetchAll');
        Route::get('/fetch-trash', [SupplierController::class, 'fetchTrash'])->name('suppliers.fetchTrash');
        Route::post('/store', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::post('/update', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::post('/delete', [SupplierController::class, 'destroy'])->name('suppliers.delete');
        Route::post('/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');
        Route::post('/permanent-delete', [SupplierController::class, 'permanentDelete'])->name('suppliers.permanentDelete');
    });

    // Product routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/fetch-all', [ProductController::class, 'fetchAll'])->name('products.fetchAll');
        Route::get('/fetch-trash', [ProductController::class, 'fetchTrash'])->name('products.fetchTrash');
        Route::post('/store', [ProductController::class, 'store'])->name('products.store');
        Route::get('/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::post('/update', [ProductController::class, 'update'])->name('products.update');
        Route::post('/delete', [ProductController::class, 'destroy'])->name('products.delete');
        Route::post('/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::post('/permanent-delete', [ProductController::class, 'permanentDelete'])->name('products.permanentDelete');
    });

    //Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/store', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/edit-data/{id}', [OrderController::class, 'editData'])->name('orders.editData');
        Route::post('/update/{id}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('/delete/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/restore/{id}', [OrderController::class, 'restore'])->name('orders.restore');
        Route::post('/force-delete/{id}', [OrderController::class, 'forceDelete'])->name('orders.forceDelete');
        Route::get('/invoice/{id}', [OrderController::class, 'invoice'])->name('orders.invoice');
    });
});
