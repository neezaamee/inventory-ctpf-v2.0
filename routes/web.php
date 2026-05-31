<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\StockIssuanceComponent;
use App\Livewire\StaffComponent;
use App\Livewire\CatalogComponent;
use App\Livewire\SupplierComponent;
use App\Livewire\GrnComponent;
use App\Livewire\ReturnComponent;
use App\Livewire\AuditLogComponent;

// Guest login routes
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Secure protected routes
Route::middleware(['auth'])->group(function () {
    // Primary Dashboards
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class);

    // Livewire Active Components
    Route::get('/issuance/create', StockIssuanceComponent::class)->name('issuance.create');
    Route::get('/staff', StaffComponent::class)->name('staff.index');
    Route::get('/items', CatalogComponent::class)->name('items.index');
    Route::get('/suppliers', SupplierComponent::class)->name('suppliers.index');
    Route::get('/grn', GrnComponent::class)->name('grn.index');
    Route::get('/returns', ReturnComponent::class)->name('returns.index');
    Route::get('/audits', AuditLogComponent::class)->name('audits.index');

    // Secure logout handler
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
