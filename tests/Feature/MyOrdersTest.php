<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MyOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('my-orders.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_customer_sees_only_their_own_orders_and_items(): void
    {
        $customer = $this->createCustomer();
        $otherCustomer = $this->createCustomer();
        $product = $this->createProduct();
        $ownOrder = $this->createOrder($customer, $product);
        $otherOrder = $this->createOrder($otherCustomer, $product);
        $cart = Cart::create(['user_id' => $customer->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($customer)->get(route('my-orders.index'))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Orders/MyOrders')
                ->where('customerName', $customer->name)
                ->where('cart.itemCount', 1)
                ->has('orders.data', 1)
                ->where('orders.data.0.id', $ownOrder->id)
                ->where('orders.data.0.total', '25.00')
                ->where('orders.data.0.items.0.productName', $product->name)
                ->where('orders.data.0.items.0.quantity', 2)
                ->missing('orders.data.1.id'));

        $this->assertNotSame($ownOrder->id, $otherOrder->id);
    }

    public function test_non_customer_cannot_access_my_orders(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));

        $this->actingAs($admin)->get(route('my-orders.index'))
            ->assertForbidden();
    }

    private function createCustomer(): User
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        return $customer;
    }

    private function createProduct(): Product
    {
        $category = ProductCategory::create(['name' => 'Pain Relief']);

        return Product::create([
            'product_category_id' => $category->id,
            'name' => 'Paracetamol 500mg',
            'price' => '12.50',
            'stock' => 10,
        ]);
    }

    private function createOrder(User $customer, Product $product): Order
    {
        $order = Order::create([
            'user_id' => $customer->id,
            'order_date' => today(),
            'total_amount' => '25.00',
            'status' => 'pending',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price_at_purchase' => '12.50',
        ]);

        return $order;
    }
}
