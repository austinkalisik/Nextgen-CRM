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
    Route::resource('customers', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('hosting-requests', DomainHostingRequestController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('users', UserManagementController::class)->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
