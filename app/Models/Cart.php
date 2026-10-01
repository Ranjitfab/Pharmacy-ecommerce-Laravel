<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * @return array{items: Collection<int, array{id: int, productId: int, name: string, price: string, stock: int, available: bool, quantity: int, lineTotal: string}>, itemCount: int, subtotal: string}
     */
    public function summary(): array
    {
        $this->loadMissing('items.product');

        $items = $this->items->map(function (CartItem $item): array {
            $product = $item->product;
            $unitPriceCents = (int) round((float) $product->price * 100);
            $expirationDate = $product->expiration_date;

            return [
                'id' => (int) $item->id,
                'productId' => (int) $product->id,
                'name' => $product->name,
                'price' => number_format($unitPriceCents / 100, 2, '.', ''),
                'stock' => (int) $product->stock,
                'available' => $product->stock > 0
                    && ($expirationDate === null || $expirationDate >= today()->toDateString()),
                'quantity' => (int) $item->quantity,
                'lineTotal' => number_format($unitPriceCents * $item->quantity / 100, 2, '.', ''),
            ];
        });

        $subtotalCents = $items->sum(
            fn(array $item): int => (int) round((float) $item['lineTotal'] * 100)
        );

        return [
            'items' => $items->values(),
            'itemCount' => (int) $items->sum('quantity'),
            'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
        ];
    }
}
