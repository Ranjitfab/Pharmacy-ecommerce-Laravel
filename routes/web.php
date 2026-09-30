<?php

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

Route::get('/dashboard', function () {
    $today = today()->toDateString();
    $expiringSoon = today()->addDays(30)->toDateString();

    $expiredAlerts = Product::query()
        ->whereDate('expiration_date', '<', $today)
        ->orderBy('expiration_date')
        ->limit(5)
        ->get(['name', 'expiration_date'])
        ->map(fn(Product $product): array => [
            'type' => 'expired',
            'title' => 'Expired product',
            'message' => $product->name . ' expired on ' . $product->expiration_date,
        ]);

    $expiringSoonAlerts = Product::query()
        ->whereDate('expiration_date', '>=', $today)
        ->whereDate('expiration_date', '<=', $expiringSoon)
        ->orderBy('expiration_date')
        ->limit(5)
        ->get(['name', 'expiration_date'])
        ->map(fn(Product $product): array => [
            'type' => 'expiring_soon',
            'title' => 'Expiring soon',
            'message' => $product->name . ' expires on ' . $product->expiration_date,
        ]);

    $lowStockAlerts = Product::query()
        ->where('stock', '<=', 5)
        ->orderBy('stock')
        ->limit(5)
        ->get(['name', 'stock'])
        ->map(fn(Product $product): array => [
            'type' => 'low_stock',
            'title' => 'Low stock',
            'message' => $product->name . ' has ' . $product->stock . ' units remaining',
        ]);

    return Inertia::render('Dashboard', [
        'adminName' => request()->user()->name,
        'stats' => [
            'products' => Product::count(),
            'orders' => Order::count(),
            'customers' => User::role('user')->count(),
        ],
        'alerts' => $expiredAlerts
            ->concat($expiringSoonAlerts)
            ->concat($lowStockAlerts)
            ->values(),
    ]);
})->middleware(['auth', 'role:admin'])->name('dashboard');

require __DIR__ . '/product-categories.php';
require __DIR__ . '/products.php';
require __DIR__ . '/suppliers.php';
