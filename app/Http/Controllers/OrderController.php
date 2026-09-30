<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        return view('orders.index', compact('orders'));
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
            return back()->with('error', 'Error placing order: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Order status updated');
    }
}