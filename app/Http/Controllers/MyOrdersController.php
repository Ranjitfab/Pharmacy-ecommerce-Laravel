<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MyOrdersController extends Controller
{
    public function index(Request $request): Response
    {
        $cart = Cart::query()
            ->with('items.product')
            ->where('user_id', $request->user()->id)
            ->first();

        $orders = $request->user()
            ->orders()
            ->with(['items.product:id,name'])
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->through(fn(Order $order): array => [
                'id' => $order->id,
                'date' => $order->order_date->format('M j, Y'),
                'status' => $order->status,
                'total' => number_format((float) $order->total_amount, 2, '.', ''),
                'items' => $order->items->map(fn(OrderItem $item): array => [
                    'productName' => $item->product?->name ?? 'Removed product',
                    'quantity' => $item->quantity,
                    'unitPrice' => number_format((float) $item->price_at_purchase, 2, '.', ''),
                ])->values(),
            ]);

        return Inertia::render('Orders/MyOrders', [
            'customerName' => $request->user()->name,
            'cart' => $cart?->summary() ?? [
                'items' => [],
                'itemCount' => 0,
                'subtotal' => '0.00',
            ],
            'orders' => $orders,
        ]);
    }
}
