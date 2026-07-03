<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('properties')->name('properties.')->group(function (): void {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/create', [PropertyController::class, 'create'])->middleware('role:admin,manager')->name('create');
        Route::post('/', [PropertyController::class, 'store'])->middleware('role:admin,manager')->name('store');
        Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->middleware('role:admin,manager')->name('edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->middleware('role:admin,manager')->name('update');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->middleware('role:admin,manager')->name('destroy');
        Route::get('/{property}/buildings/{building}', [PropertyController::class, 'building'])->name('building');
        Route::get('/{property}/buildings/{building}/rooms/{room}', [PropertyController::class, 'room'])->name('room');
    });

    Route::prefix('tenants')->name('tenants.')->group(function (): void {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::get('/create', [TenantController::class, 'create'])->middleware('role:admin,manager')->name('create');
        Route::post('/', [TenantController::class, 'store'])->middleware('role:admin,manager')->name('store');
        Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
        Route::get('/{tenant}/edit', [TenantController::class, 'edit'])->middleware('role:admin,manager')->name('edit');
        Route::put('/{tenant}', [TenantController::class, 'update'])->middleware('role:admin,manager')->name('update');
        Route::delete('/{tenant}', [TenantController::class, 'destroy'])->middleware('role:admin,manager')->name('destroy');
    });

    Route::prefix('payments')->name('payments.')->group(function (): void {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->middleware('role:admin,manager')->name('create');
        Route::post('/', [PaymentController::class, 'store'])->middleware('role:admin,manager')->name('store');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->middleware('role:admin,manager')->name('edit');
        Route::put('/{payment}', [PaymentController::class, 'update'])->middleware('role:admin,manager')->name('update');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->middleware('role:admin,manager')->name('destroy');
    });
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
});
