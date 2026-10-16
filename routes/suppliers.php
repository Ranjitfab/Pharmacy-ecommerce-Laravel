<?php

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin|manager'])
    ->group(function () {
        Route::resource('suppliers', SupplierController::class)->except('destroy');

        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('suppliers.destroy');
    });
