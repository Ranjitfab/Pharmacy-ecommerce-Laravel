<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', Rule::exists('product_categories', 'id')],
            'sort' => ['nullable', Rule::in(['newest', 'price_asc', 'price_desc', 'name'])],
            'availability' => ['nullable', Rule::in(['all', 'in_stock', 'sold_out'])],
        ]);

        $search = trim($filters['search'] ?? '');
        $categoryId = $filters['category'] ?? null;
        $sort = $filters['sort'] ?? 'newest';
        $availability = $filters['availability'] ?? 'all';

        $products = Product::query()
            ->with('category:id,name')
            ->where(function (Builder $query): void {
                $query->whereNull('expiration_date')
                    ->orWhereDate('expiration_date', '>=', today());
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('dosage', 'like', '%'.$search.'%')
                        ->orWhere('strength', 'like', '%'.$search.'%');
                });
            })
            ->when($categoryId, fn (Builder $query): Builder => $query->where('product_category_id', $categoryId))
            ->when($availability === 'in_stock', fn (Builder $query): Builder => $query->where('stock', '>', 0))
            ->when($availability === 'sold_out', fn (Builder $query): Builder => $query->where('stock', 0));

        match ($sort) {
            'price_asc' => $products->orderBy('price')->orderBy('name'),
            'price_desc' => $products->orderByDesc('price')->orderBy('name'),
            'name' => $products->orderBy('name'),
            default => $products->latest(),
        };

        $cart = Cart::query()
            ->with('items.product')
            ->where('user_id', $request->user()->id)
            ->first();
        $wishlistProductIds = $request->user()->wishlist()->pluck('products.id')->all();

        return Inertia::render('Shop', [
            'customerName' => $request->user()->name,
            'products' => $products->paginate(12)->withQueryString(),
            'categories' => ProductCategory::query()->orderBy('name')->get(['id', 'name']),
            'cart' => $cart?->summary() ?? [
                'items' => [],
                'itemCount' => 0,
                'subtotal' => '0.00',
            ],
            'wishlistProductIds' => $wishlistProductIds,
            'wishlistCount' => count($wishlistProductIds),
            'success' => $request->session()->get('success'),
            'filters' => [
                'search' => $search,
                'category' => $categoryId,
                'sort' => $sort,
                'availability' => $availability,
            ],
        ]);
    }
}
