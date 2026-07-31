<?php

use App\Http\Controllers\ProfileController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->email == 'eslam@gmail.com') {
        # code...
        $customers = Customer::all();
    } else {
        $customers = auth()->user()->customers()->latest()->paginate(10);
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
});

require __DIR__ . '/auth.php';
