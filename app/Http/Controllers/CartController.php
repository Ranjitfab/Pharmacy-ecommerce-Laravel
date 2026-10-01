<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $cart = $this->lockCustomerCart($request->user());
            $product = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);

            $this->ensureProductIsAvailable($product);

            $cartItem = $cart->items()->where('product_id', $product->id)->first();
            $quantity = ($cartItem?->quantity ?? 0) + $validated['quantity'];

            if ($quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'There is not enough stock for that quantity.',
                ]);
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $quantity]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }
        });

        return back()->with('success', 'Product added to your cart.');
    }

    public function update(Request $request, int $cartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request, $cartItem, $validated): void {
            $cart = $this->lockCustomerCart($request->user());
            $item = $cart->items()->whereKey($cartItem)->firstOrFail();
            $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);

            $this->ensureProductIsAvailable($product);

            if ($validated['quantity'] > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'There is not enough stock for that quantity.',
                ]);
            }

            $item->update(['quantity' => $validated['quantity']]);
        });

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, int $cartItem): RedirectResponse
    {
        DB::transaction(function () use ($request, $cartItem): void {
            $cart = $this->lockCustomerCart($request->user());
            $cart->items()->whereKey($cartItem)->delete();
        });

        return back()->with('success', 'Item removed from your cart.');
    }

    private function lockCustomerCart(User $user): Cart
    {
        User::query()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();

        $cart = Cart::query()->firstOrCreate(['user_id' => $user->getKey()]);

        return Cart::query()->whereKey($cart->getKey())->lockForUpdate()->firstOrFail();
    }

    private function ensureProductIsAvailable(Product $product): void
    {
        $isExpired = $product->expiration_date !== null
            && $product->expiration_date < today()->toDateString();

        if ($product->stock < 1 || $isExpired) {
            throw ValidationException::withMessages([
                'cart' => 'This product is currently unavailable.',
            ]);
        }
    }
}
