<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('businesses.select'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.select');
    Route::get('/businesses/create', [BusinessController::class, 'create'])->middleware('role:admin,manager')->name('businesses.create');
    Route::post('/businesses', [BusinessController::class, 'store'])->middleware('role:admin,manager')->name('businesses.store');
    Route::get('/businesses/{business}/open', [BusinessController::class, 'open'])->name('businesses.open');
    Route::post('/businesses/{business}/daily-entry', [DashboardController::class, 'storeDailyEntry'])->middleware('role:admin,manager')->name('businesses.daily-entry.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('properties')->name('properties.')->group(function (): void {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/create', [PropertyController::class, 'create'])->middleware('role:admin,manager')->name('create');
        Route::post('/', [PropertyController::class, 'store'])->middleware('role:admin,manager')->name('store');
        Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->middleware('role:admin,manager')->name('edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->middleware('role:admin,manager')->name('update');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->middleware('role:admin,manager')->name('destroy');
        Route::get('/{property}/buildings/create', [PropertyController::class, 'createBuilding'])->middleware('role:admin,manager')->name('buildings.create');
        Route::post('/{property}/buildings', [PropertyController::class, 'storeBuilding'])->middleware('role:admin,manager')->name('buildings.store');
        Route::get('/{property}/buildings/{building}', [PropertyController::class, 'building'])->name('building');
        Route::get('/{property}/buildings/{building}/edit', [PropertyController::class, 'editBuilding'])->middleware('role:admin,manager')->name('buildings.edit');
        Route::put('/{property}/buildings/{building}', [PropertyController::class, 'updateBuilding'])->middleware('role:admin,manager')->name('buildings.update');
        Route::get('/{property}/buildings/{building}/rooms/create', [PropertyController::class, 'createRoom'])->middleware('role:admin,manager')->name('rooms.create');
        Route::post('/{property}/buildings/{building}/rooms', [PropertyController::class, 'storeRoom'])->middleware('role:admin,manager')->name('rooms.store');
        Route::get('/{property}/buildings/{building}/rooms/{room}', [PropertyController::class, 'room'])->name('room');
        Route::get('/{property}/buildings/{building}/rooms/{room}/edit', [PropertyController::class, 'editRoom'])->middleware('role:admin,manager')->name('rooms.edit');
        Route::put('/{property}/buildings/{building}/rooms/{room}', [PropertyController::class, 'updateRoom'])->middleware('role:admin,manager')->name('rooms.update');
        Route::post('/{property}/buildings/{building}/rooms/{room}/assign', [PropertyController::class, 'assignTenant'])->middleware('role:admin,manager')->name('rooms.assign');
        Route::post('/{property}/buildings/{building}/rooms/{room}/vacate', [PropertyController::class, 'vacateTenant'])->middleware('role:admin,manager')->name('rooms.vacate');
    });

    Route::prefix('tenants')->name('tenants.')->group(function (): void {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::get('/create', [TenantController::class, 'create'])->middleware('role:admin,manager')->name('create');
        Route::post('/', [TenantController::class, 'store'])->middleware('role:admin,manager')->name('store');
        Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
        Route::get('/{tenant}/edit', [TenantController::class, 'edit'])->middleware('role:admin,manager')->name('edit');
        Route::put('/{tenant}', [TenantController::class, 'update'])->middleware('role:admin,manager')->name('update');
        Route::delete('/{tenant}', [TenantController::class, 'destroy'])->middleware('role:admin,manager')->name('destroy');
        Route::post('/{tenant}/rooms/{room}/renew', [TenantController::class, 'renewRoomLease'])->middleware('role:admin,manager')->name('rooms.renew');
        Route::post('/{tenant}/contract', [TenantController::class, 'uploadContract'])->middleware('role:admin,manager')->name('contract.upload');
        Route::get('/{tenant}/contract', [TenantController::class, 'downloadContract'])->middleware('role:admin,manager')->name('contract.download');
    });

    Route::prefix('payments')->name('payments.')->group(function (): void {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->middleware('role:admin,manager')->name('create');
        Route::post('/', [PaymentController::class, 'store'])->middleware('role:admin,manager')->name('store');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->middleware('role:admin,manager')->name('edit');
        Route::put('/{payment}', [PaymentController::class, 'update'])->middleware('role:admin,manager')->name('update');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->middleware('role:admin,manager')->name('destroy');
        Route::post('/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->middleware('role:admin,manager')->name('markPaid');
    });
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
});
