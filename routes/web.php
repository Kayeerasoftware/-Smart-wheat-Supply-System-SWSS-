<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    $role = $user->role;

    switch ($role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'farmer':
            return redirect()->route('farmer.dashboard');
        case 'supplier':
            return redirect()->route('supplier.dashboard');
        case 'manufacturer':
            return redirect()->route('manufacturer.dashboard');
        case 'distributor':
            return redirect()->route('distributor.dashboard');
        case 'retailer':
            return redirect()->route('retailer.dashboard');
        case 'vendor':
            return redirect()->route('vendor.dashboard');
        default:
            return redirect()->route('dashboard');
    }
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/farmer/dashboard', [DashboardController::class, 'farmer'])->name('farmer.dashboard');
    Route::get('/supplier/dashboard', [DashboardController::class, 'supplier'])->name('supplier.dashboard');
    Route::get('/manufacturer/dashboard', [DashboardController::class, 'manufacturer'])->name('manufacturer.dashboard');
    Route::get('/distributor/dashboard', [DashboardController::class, 'distributor'])->name('distributor.dashboard');
    Route::get('/retailer/dashboard', [DashboardController::class, 'retailer'])->name('retailer.dashboard');
    Route::get('/vendor/dashboard', [DashboardController::class, 'vendor'])->name('vendor.dashboard');
    Route::get('/vendor/application', [App\Http\Controllers\VendorController::class, 'showApplicationForm'])->name('vendor.application');
    Route::post('/vendor/application', [App\Http\Controllers\VendorController::class, 'submitApplication'])->name('vendor.application.submit');
    
    // Admin vendor management routes
    Route::prefix('admin/vendors')->group(function () {
        Route::post('/{vendor}/schedule-visit', [App\Http\Controllers\AdminController::class, 'scheduleFacilityVisit'])->name('admin.vendors.schedule-visit');
        Route::post('/{vendor}/approve', [App\Http\Controllers\AdminController::class, 'approveVendor'])->name('admin.vendors.approve');
        Route::post('/{vendor}/reject', [App\Http\Controllers\AdminController::class, 'rejectVendor'])->name('admin.vendors.reject');
        Route::get('/{vendor}/details', [App\Http\Controllers\AdminController::class, 'viewVendorDetails'])->name('admin.vendors.details');
        Route::put('/{vendor}/scores', [App\Http\Controllers\AdminController::class, 'updateVendorScores'])->name('admin.vendors.update-scores');
    });

    // Vendor routes
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/dashboard', [VendorController::class, 'showStatus'])->name('dashboard');
        Route::get('/application', [VendorController::class, 'showApplicationForm'])->name('application');
        Route::post('/application', [VendorController::class, 'submitApplication'])->name('application.submit');
        Route::get('/document/{type}', [VendorController::class, 'downloadDocument'])->name('document.download');
    });

    // Product Management Routes
    Route::middleware('auth')->prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/low-stock/alerts', [ProductController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('/analytics', [ProductController::class, 'analytics'])->name('analytics');
    });

    // Category Management Routes
    Route::middleware('auth')->prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('/tree', [CategoryController::class, 'tree'])->name('tree');
        Route::get('/analytics', [CategoryController::class, 'analytics'])->name('analytics');
    });

    // Warehouse Management Routes
    Route::middleware('auth')->prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{warehouse}', [WarehouseController::class, 'show'])->name('show');
        Route::get('/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
        Route::get('/analytics', [WarehouseController::class, 'analytics'])->name('analytics');
        Route::get('/{warehouse}/inventory-report', [WarehouseController::class, 'inventoryReport'])->name('inventory-report');
    });

    // Inventory Management Routes
    Route::middleware('auth')->prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/low-stock/alerts', [InventoryController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('/analytics', [InventoryController::class, 'analytics'])->name('analytics');
        Route::post('/bulk-adjustment', [InventoryController::class, 'bulkAdjustment'])->name('bulk-adjustment');
    });
});

require __DIR__.'/auth.php';