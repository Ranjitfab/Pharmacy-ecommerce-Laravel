<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $cart = Cart::query()
            ->with('items.product')
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        return Inertia::render('Checkout', [
            'customerName' => $request->user()->name,
            'cart' => $cart->summary(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $order = DB::transaction(function () use ($request): Order {
            $user = $request->user();
            User::query()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();

            $cart = Cart::query()
                ->where('user_id', $user->getKey())
                ->lockForUpdate()
                ->first();

            if (! $cart) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            $items = $cart->items()->orderBy('id')->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            $products = Product::query()
                ->whereKey($items->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalCents = 0;
            $orderLines = [];

            foreach ($items as $item) {
                $product = $products->get($item->product_id);

                if (! $product || $product->stock < $item->quantity || $this->isExpired($product)) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stock changed for an item in your cart. Review your cart and try again.',
                    ]);
                }

                $priceCents = (int) round((float) $product->price * 100);
                $totalCents += $priceCents * $item->quantity;
                $orderLines[] = [
                    'product' => $product,
                    'quantity' => $item->quantity,
                    'price' => number_format($priceCents / 100, 2, '.', ''),
                ];
            }

            $order = Order::create([
                'user_id' => $user->getKey(),
                'order_date' => today()->toDateString(),
                'total_amount' => number_format($totalCents / 100, 2, '.', ''),
                'status' => 'pending',
            ]);

            foreach ($orderLines as $line) {
                $line['product']->decrement('stock', $line['quantity']);
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'price_at_purchase' => $line['price'],
                ]);
            }

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('shop.index')->with(
            'success',
            'Order #' . $order->id . ' placed. Its status is pending.'
        );
    }

    private function isExpired(Product $product): bool
    {
        return $product->expiration_date !== null
            && $product->expiration_date < today()->toDateString();
    }
}
