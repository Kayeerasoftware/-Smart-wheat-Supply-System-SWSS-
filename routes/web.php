<?php

use App\Http\Controllers\DistributorController;
use Illuminate\Support\Facades\Route;

Route::get('/distributor/dashboard', [DistributorController::class, 'index'])->name('distributor.dashboard');
Route::post('/distributor', [DistributorController::class, 'store'])->name('distributor.store');