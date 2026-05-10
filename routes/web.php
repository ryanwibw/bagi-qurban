<?php

use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Coupon Actions (Statis, letakkan di atas wildcard)
    Route::get('/coupon/batch/print', [CouponController::class, 'batchPrint'])->name('coupon.print');
    Route::get('/coupon/scan', [CouponController::class, 'scan'])->name('coupon.scan');
    Route::post('/coupon/claim', [CouponController::class, 'claim'])->name('coupon.claim');
    Route::get('/coupon/create', [CouponController::class, 'create'])->name('coupon.create');
    Route::post('/coupon/approve-all', [CouponController::class, 'approveAll'])->name('coupon.approve-all');

    // Coupon Management
    Route::get('/coupon', [CouponController::class, 'index'])->name('coupon.index');

    // Rute wildcard (paling bawah)
    Route::get('/coupon/{coupon}', [CouponController::class, 'show'])->name('coupon.show');
    Route::post('/coupon', [CouponController::class, 'store'])->name('coupon.store');
    Route::patch('/coupon/{coupon}/approve', [CouponController::class, 'approve'])->name('coupon.approve');
    Route::delete('/coupon/{coupon}', [CouponController::class, 'destroy'])->name('coupon.destroy');

    // Panitia Management (Admin Only)
    Route::get('/panitia', [UserController::class, 'index'])->name('panitia.index');
    Route::get('/panitia/create', [UserController::class, 'create'])->name('panitia.create');
    Route::post('/panitia', [UserController::class, 'store'])->name('panitia.store');
    Route::delete('/panitia/{user}', [UserController::class, 'destroy'])->name('panitia.destroy');

    // Organization Profile
    Route::get('/organization', [OrganizationController::class, 'edit'])->name('organization.edit');
    Route::patch('/organization', [OrganizationController::class, 'update'])->name('organization.update');

    // Recapitulation
    Route::get('/recapitulation', [\App\Http\Controllers\RecapController::class, 'index'])->name('recap.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
