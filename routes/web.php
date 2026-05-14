<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Crm\CustomerController;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\DomainHostingRequestController;
use App\Http\Controllers\Crm\UserManagementController;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('renewals', [DashboardController::class, 'renewals'])->name('renewals');
    Route::post('renewals/update', [DashboardController::class, 'updateRenewals'])->name('renewals.update');
    Route::get('add-customer', [CustomerController::class, 'create'])->name('customers.create');
    Route::get('support-requests', [DomainHostingRequestController::class, 'support'])->name('support-requests');
    Route::get('domain-registrations', [DomainHostingRequestController::class, 'registrations'])->name('domain-registrations');
    Route::get('bulk-email-validator', [DashboardController::class, 'bulkEmailValidator'])->name('bulk-email-validator');
    Route::post('bulk-email-validator', [DashboardController::class, 'validateEmails'])->name('bulk-email-validator.validate');
    Route::get('admin-settings', [UserManagementController::class, 'legacySettings'])->name('admin-settings');
    Route::patch('admin-settings', [UserManagementController::class, 'updateLegacySettings'])->name('admin-settings.update');
    Route::resource('customers', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('hosting-requests', DomainHostingRequestController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('users', UserManagementController::class)->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
