<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->with(['user:id,name', 'items.product:id,name'])
            ->orderByDesc('id')
            ->paginate(15)
            ->through(fn (Order $order): array => [
                'id' => $order->id,
                'customerName' => $order->user?->name ?? 'Unknown customer',
                'date' => $order->order_date?->format('M j, Y'),
                'totalAmount' => number_format((float) $order->total_amount, 2, '.', ''),
                'status' => $order->status,
                'items' => $order->items->map(fn (OrderItem $item): array => [
                    'productName' => $item->product?->name ?? 'Deleted product',
                    'quantity' => $item->quantity,
                    'price' => number_format((float) $item->price_at_purchase, 2, '.', ''),
                    'subtotal' => number_format((float) $item->price_at_purchase * $item->quantity, 2, '.', ''),
                ])->values(),
            ]);

        return Inertia::render('Orders/Index', [
            'adminName' => $request->user()->name,
            'orders' => $orders,
            'success' => $request->session()->get('success'),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('orders.show', compact('order'));
    }

    // Logic for placing an order
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $price = $product->price;
                $totalAmount += $price * $item['quantity'];

                $orderItemsData[] = new OrderItem([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $price,
                ]);

                // Deduct stock
                $product->decrement('stock', $item['quantity']);
            }

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_date' => now(),
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            $order->items()->saveMany($orderItemsData);

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error placing order: '.$e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'shipped', 'delivered', 'cancelled'])],
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->route('orders.index')->with('success', 'Order status updated.');
    }
}
