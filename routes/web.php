<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManufacturerDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
});

// Manufacturer Routes
Route::middleware(['auth', 'role:manufacturer'])->prefix('manufacturer')->name('manufacturer.')->group(function () {
    Route::get('/dashboard', [ManufacturerDashboardController::class, 'index'])->name('dashboard');
    
    // Production Lines
    Route::get('/production-lines', [ManufacturerDashboardController::class, 'productionLines'])->name('production-lines');
    Route::get('/production-lines/create', [ManufacturerDashboardController::class, 'createProductionLine'])->name('production-lines.create');
    Route::post('/production-lines', [ManufacturerDashboardController::class, 'storeProductionLine'])->name('production-lines.store');
    Route::get('/production-lines/{productionLine}/edit', [ManufacturerDashboardController::class, 'editProductionLine'])->name('production-lines.edit');
    Route::put('/production-lines/{productionLine}', [ManufacturerDashboardController::class, 'updateProductionLine'])->name('production-lines.update');
    
    // Quality Checks
    Route::get('/quality-checks', [ManufacturerDashboardController::class, 'qualityChecks'])->name('quality-checks');
    Route::get('/quality-checks/create', [ManufacturerDashboardController::class, 'createQualityCheck'])->name('quality-checks.create');
    Route::post('/quality-checks', [ManufacturerDashboardController::class, 'storeQualityCheck'])->name('quality-checks.store');
    Route::get('/quality-checks/{qualityCheck}/edit', [ManufacturerDashboardController::class, 'editQualityCheck'])->name('quality-checks.edit');
    Route::put('/quality-checks/{qualityCheck}', [ManufacturerDashboardController::class, 'updateQualityCheck'])->name('quality-checks.update');
    
    // Raw Materials
    Route::get('/raw-materials', [ManufacturerDashboardController::class, 'rawMaterials'])->name('raw-materials');
    Route::get('/raw-materials/create', [ManufacturerDashboardController::class, 'createRawMaterial'])->name('raw-materials.create');
    Route::post('/raw-materials', [ManufacturerDashboardController::class, 'storeRawMaterial'])->name('raw-materials.store');
    Route::get('/raw-materials/{rawMaterial}/edit', [ManufacturerDashboardController::class, 'editRawMaterial'])->name('raw-materials.edit');
    Route::put('/raw-materials/{rawMaterial}', [ManufacturerDashboardController::class, 'updateRawMaterial'])->name('raw-materials.update');

    // Orders
    Route::get('/orders/create', [ManufacturerDashboardController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders', [ManufacturerDashboardController::class, 'storeOrder'])->name('orders.store');
});

require __DIR__.'/auth.php';