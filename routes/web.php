<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('properties')->name('properties.')->group(function () {
    Route::get('/', [PropertyController::class, 'index'])->name('index');
    Route::get('/{id}', [PropertyController::class, 'show'])->name('show');
    Route::get('/{propertyId}/buildings/{buildingId}', [PropertyController::class, 'building'])->name('building');
    Route::get('/{propertyId}/buildings/{buildingId}/rooms/{roomId}', [PropertyController::class, 'room'])->name('room');
});

Route::prefix('tenants')->name('tenants.')->group(function () {
    Route::get('/', [TenantController::class, 'index'])->name('index');
    Route::get('/{id}', [TenantController::class, 'show'])->name('show');
});

Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
