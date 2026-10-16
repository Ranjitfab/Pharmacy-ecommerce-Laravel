<?php

use App\Actions\Inventory\GetInventoryAlerts;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/react-demo', function () {
    return Inertia::render('Welcome');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/shop', ShopController::class)->name('shop.index');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.items.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.items.destroy');
    Route::post('/cart/items', [CartController::class, 'add'])->name('cart.items.store');
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'remove'])->name('cart.items.destroy');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

Route::middleware(['auth', 'role:admin|manager'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('/notifications', function (GetInventoryAlerts $getInventoryAlerts) {
        return Inertia::render('Notifications/Index', [
            'alerts' => $getInventoryAlerts->handle(),
        ]);
    })->name('notifications.index');
});

Route::get('/dashboard', function (GetInventoryAlerts $getInventoryAlerts) {
    return Inertia::render('Dashboard', [
        'adminName' => request()->user()->name,
        'stats' => [
            'products' => Product::count(),
            'orders' => Order::count(),
            'customers' => User::role('user')->count(),
        ],
        'alerts' => $getInventoryAlerts->handle(),
    ]);
})->middleware(['auth', 'role:admin|manager'])->name('dashboard');

require __DIR__.'/product-categories.php';
require __DIR__.'/products.php';
require __DIR__.'/suppliers.php';
