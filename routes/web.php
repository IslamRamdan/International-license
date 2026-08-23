<?php

use App\Http\Controllers\ProfileController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->email == 'eslam@gmail.com') {
        $customers = Customer::orderByRaw("CASE WHEN status = 'admin' THEN 0 ELSE 1 END")
            ->latest()
            ->get();
    } else {
        $customers = auth()->user()->customers()->latest()->get();
    }

    return view('dashboard', compact('customers'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\CustomerController;

Route::middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::patch('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');
    Route::patch('/customers/{customer}/to-admin', [CustomerController::class, 'toAdmin'])->name('customers.toAdmin');
});

require __DIR__ . '/auth.php';
