<?php

namespace App\Actions\Inventory;

use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetInventoryAlerts
{
    public function handle(): Collection
    {
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
                'message' => $product->name . ' expired on ' . Carbon::parse($product->expiration_date)->toDateString(),
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
                'message' => $product->name . ' expires on ' . Carbon::parse($product->expiration_date)->toDateString(),
            ]);

        $lowStockAlerts = Product::query()
            ->whereColumn('stock', '<=', 'reorder_threshold')
            ->orderBy('stock')
            ->limit(5)
            ->get(['name', 'stock', 'reorder_threshold'])
            ->map(fn(Product $product): array => [
                'type' => 'low_stock',
                'title' => 'Low stock',
                'message' => $product->name . ' has ' . $product->stock . ' units remaining (reorder threshold: ' . $product->reorder_threshold . ')',
            ]);

        return $expiredAlerts
            ->concat($expiringSoonAlerts)
            ->concat($lowStockAlerts)
            ->values();
    }
}
