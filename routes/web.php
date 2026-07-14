<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('monitoring.rental'));

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
        Route::delete('/{property}/buildings/{building}', [PropertyController::class, 'destroyBuilding'])->middleware('role:admin,manager')->name('buildings.destroy');
        Route::get('/{property}/buildings/{building}/rooms/create', [PropertyController::class, 'createRoom'])->middleware('role:admin,manager')->name('rooms.create');
        Route::post('/{property}/buildings/{building}/rooms', [PropertyController::class, 'storeRoom'])->middleware('role:admin,manager')->name('rooms.store');
        Route::get('/{property}/buildings/{building}/rooms/{room}', [PropertyController::class, 'room'])->name('room');
        Route::get('/{property}/buildings/{building}/rooms/{room}/edit', [PropertyController::class, 'editRoom'])->middleware('role:admin,manager')->name('rooms.edit');
        Route::put('/{property}/buildings/{building}/rooms/{room}', [PropertyController::class, 'updateRoom'])->middleware('role:admin,manager')->name('rooms.update');
        Route::delete('/{property}/buildings/{building}/rooms/{room}', [PropertyController::class, 'destroyRoom'])->middleware('role:admin,manager')->name('rooms.destroy');
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

    Route::prefix('expenses')->name('expenses.')->group(function (): void {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->middleware('role:admin,manager')->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->middleware('role:admin,manager')->name('store');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->middleware('role:admin,manager')->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->middleware('role:admin,manager')->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->middleware('role:admin,manager')->name('destroy');
    });

    Route::post('/properties/{property}/buildings/{building}/rooms/{room}/toggle-maintenance', [PropertyController::class, 'toggleMaintenance'])->middleware('role:admin,manager')->name('properties.rooms.toggle-maintenance');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

    Route::prefix('monitoring')->name('monitoring.')->group(function (): void {
        Route::get('/rental', [MonitoringController::class, 'rental'])->name('rental');
        Route::get('/rental/report', [MonitoringController::class, 'rentalReport'])->name('rental.report');
        Route::get('/agriculture', [MonitoringController::class, 'agriculture'])->name('agriculture');
        Route::get('/agriculture/sales/create', [MonitoringController::class, 'createAgricultureSales'])->name('agriculture.sales.create');
        Route::get('/agriculture/sales/{transaction}/edit', [MonitoringController::class, 'editAgricultureSales'])->name('agriculture.sales.edit');
        Route::post('/agriculture/sales', [MonitoringController::class, 'storeAgricultureSales'])->name('agriculture.sales.store');
        Route::put('/agriculture/sales/{transaction}', [MonitoringController::class, 'updateAgricultureSales'])->name('agriculture.sales.update');
        Route::delete('/agriculture/sales/{transaction}', [MonitoringController::class, 'destroyAgricultureSales'])->name('agriculture.sales.destroy');
        Route::get('/agriculture/expenses/create', [MonitoringController::class, 'createAgricultureExpenses'])->name('agriculture.expenses.create');
        Route::get('/agriculture/expenses/{transaction}/edit', [MonitoringController::class, 'editAgricultureExpenses'])->name('agriculture.expenses.edit');
        Route::post('/agriculture/expenses', [MonitoringController::class, 'storeAgricultureExpenses'])->name('agriculture.expenses.store');
        Route::put('/agriculture/expenses/{transaction}', [MonitoringController::class, 'updateAgricultureExpenses'])->name('agriculture.expenses.update');
        Route::delete('/agriculture/expenses/{transaction}', [MonitoringController::class, 'destroyAgricultureExpenses'])->name('agriculture.expenses.destroy');
        Route::get('/tilapia', [MonitoringController::class, 'tilapia'])->name('tilapia');
        Route::get('/tilapia/sales/create', [MonitoringController::class, 'createTilapiaSales'])->name('tilapia.sales.create');
        Route::get('/tilapia/sales/{transaction}/edit', [MonitoringController::class, 'editTilapiaSales'])->name('tilapia.sales.edit');
        Route::post('/tilapia/sales', [MonitoringController::class, 'storeTilapiaSales'])->name('tilapia.sales.store');
        Route::put('/tilapia/sales/{transaction}', [MonitoringController::class, 'updateTilapiaSales'])->name('tilapia.sales.update');
        Route::delete('/tilapia/sales/{transaction}', [MonitoringController::class, 'destroyTilapiaSales'])->name('tilapia.sales.destroy');
        Route::get('/tilapia/expenses/create', [MonitoringController::class, 'createTilapiaExpenses'])->name('tilapia.expenses.create');
        Route::get('/tilapia/expenses/{transaction}/edit', [MonitoringController::class, 'editTilapiaExpenses'])->name('tilapia.expenses.edit');
        Route::post('/tilapia/expenses', [MonitoringController::class, 'storeTilapiaExpenses'])->name('tilapia.expenses.store');
        Route::put('/tilapia/expenses/{transaction}', [MonitoringController::class, 'updateTilapiaExpenses'])->name('tilapia.expenses.update');
        Route::delete('/tilapia/expenses/{transaction}', [MonitoringController::class, 'destroyTilapiaExpenses'])->name('tilapia.expenses.destroy');
        Route::get('/conel', [MonitoringController::class, 'conel'])->name('conel');
        Route::get('/conel/deposit/create', [MonitoringController::class, 'createConelDeposit'])->name('conel.deposit.create');
        Route::get('/conel/deposit/{transaction}/edit', [MonitoringController::class, 'editConelDeposit'])->name('conel.deposit.edit');
        Route::post('/conel/deposit', [MonitoringController::class, 'storeConelDeposit'])->name('conel.deposit.store');
        Route::put('/conel/deposit/{transaction}', [MonitoringController::class, 'updateConelDeposit'])->name('conel.deposit.update');
        Route::delete('/conel/deposit/{transaction}', [MonitoringController::class, 'destroyConelDeposit'])->name('conel.deposit.destroy');
        Route::get('/conel/withdraw/create', [MonitoringController::class, 'createConelWithdraw'])->name('conel.withdraw.create');
        Route::get('/conel/withdraw/{transaction}/edit', [MonitoringController::class, 'editConelWithdraw'])->name('conel.withdraw.edit');
        Route::post('/conel/withdraw', [MonitoringController::class, 'storeConelWithdraw'])->name('conel.withdraw.store');
        Route::put('/conel/withdraw/{transaction}', [MonitoringController::class, 'updateConelWithdraw'])->name('conel.withdraw.update');
        Route::delete('/conel/withdraw/{transaction}', [MonitoringController::class, 'destroyConelWithdraw'])->name('conel.withdraw.destroy');
        Route::get('/128', [MonitoringController::class, 'oneTwoEight'])->name('128');
        Route::get('/128/deposit/create', [MonitoringController::class, 'create128Deposit'])->name('128.deposit.create');
        Route::get('/128/deposit/{transaction}/edit', [MonitoringController::class, 'edit128Deposit'])->name('128.deposit.edit');
        Route::post('/128/deposit', [MonitoringController::class, 'store128Deposit'])->name('128.deposit.store');
        Route::put('/128/deposit/{transaction}', [MonitoringController::class, 'update128Deposit'])->name('128.deposit.update');
        Route::delete('/128/deposit/{transaction}', [MonitoringController::class, 'destroy128Deposit'])->name('128.deposit.destroy');
        Route::get('/128/withdraw/create', [MonitoringController::class, 'create128Withdraw'])->name('128.withdraw.create');
        Route::get('/128/withdraw/{transaction}/edit', [MonitoringController::class, 'edit128Withdraw'])->name('128.withdraw.edit');
        Route::post('/128/withdraw', [MonitoringController::class, 'store128Withdraw'])->name('128.withdraw.store');
        Route::put('/128/withdraw/{transaction}', [MonitoringController::class, 'update128Withdraw'])->name('128.withdraw.update');
        Route::delete('/128/withdraw/{transaction}', [MonitoringController::class, 'destroy128Withdraw'])->name('128.withdraw.destroy');
        Route::post('/transaction', [MonitoringController::class, 'storeTransaction'])->middleware('role:admin,manager')->name('transaction.store');
    });
});
