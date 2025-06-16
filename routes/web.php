<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
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

require __DIR__.'/auth.php';