<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertRedirectToRoute('login');
    }

    public function test_customer_cannot_access_admin_orders(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($customer)->get(route('orders.index'));

        $response->assertForbidden();
    }

    public function test_admin_sees_newest_orders_with_customer_and_item_details(): void
    {
        $admin = $this->createAdmin();
        $customer = User::factory()->create(['name' => 'Casey Customer']);
        $category = ProductCategory::create(['name' => 'Pain Relief']);
        $product = Product::create([
            'product_category_id' => $category->id,
            'name' => 'Paracetamol 500mg',
            'price' => '8.25',
            'stock' => 5,
        ]);
        $order = Order::create([
            'user_id' => $customer->id,
            'order_date' => today(),
            'total_amount' => '16.50',
            'status' => 'pending',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price_at_purchase' => '8.25',
        ]);

        $response = $this->actingAs($admin)->get(route('orders.index'));

        $response->assertInertia(
            fn(Assert $page) => $page
                ->component('Orders/Index')
                ->where('orders.data.0.id', $order->id)
                ->where('orders.data.0.customerName', 'Casey Customer')
                ->where('orders.data.0.totalAmount', '16.50')
                ->where('orders.data.0.items.0.productName', 'Paracetamol 500mg')
                ->where('orders.data.0.items.0.quantity', 2)
                ->where('orders.data.0.items.0.subtotal', '16.50')
        );
    }

    public function test_admin_can_open_the_inertia_product_page_with_admin_permissions(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)->get(route('products.index'))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Products/Index')
                ->where('auth.user.roles.0', 'admin')
                ->has('auth.user.permissions', 14));
    }

    public function test_admin_can_update_an_order_status(): void
    {
        $admin = $this->createAdmin();
        $order = $this->createOrder();

        $response = $this->actingAs($admin)->patch(route('orders.update-status', $order), [
            'status' => 'shipped',
        ]);

        $response->assertRedirectToRoute('orders.index');
        $response->assertSessionHas('success', 'Order status updated.');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
        ]);
    }

    public function test_manager_can_view_orders_and_update_their_status(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = User::factory()->create();
        $manager->assignRole(Role::findByName('manager', 'web'));
        $order = $this->createOrder();

        $this->actingAs($manager)->get(route('orders.index'))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Orders/Index')
                ->where('orders.data.0.id', $order->id));

        $this->actingAs($manager)->patch(route('orders.update-status', $order), [
            'status' => 'shipped',
        ])->assertRedirectToRoute('orders.index');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
        ]);
    }

    public function test_admin_cannot_set_an_unrecognized_order_status(): void
    {
        $admin = $this->createAdmin();
        $order = $this->createOrder();

        $response = $this->actingAs($admin)->patch(route('orders.update-status', $order), [
            'status' => 'refunded',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    private function createAdmin(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('admin', 'web'));

        return $admin;
    }

    private function createOrder(): Order
    {
        return Order::create([
            'user_id' => User::factory()->create()->id,
            'order_date' => today(),
            'total_amount' => '10.00',
            'status' => 'pending',
        ]);
    }
}
