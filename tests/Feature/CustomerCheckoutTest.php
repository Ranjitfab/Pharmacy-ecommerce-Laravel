<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_a_product_to_their_cart(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(stock: 5);

        $response = $this->actingAs($customer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_add_more_than_available_stock(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(stock: 2);

        $response = $this->actingAs($customer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_customer_cannot_add_a_sold_out_product(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(stock: 0);

        $response = $this->actingAs($customer)->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_customer_cannot_update_another_customers_cart_item(): void
    {
        $owner = $this->createCustomer();
        $otherCustomer = $this->createCustomer();
        $product = $this->createProduct(stock: 5);
        $cart = Cart::create(['user_id' => $owner->id]);
        $item = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($otherCustomer)->patch(route('cart.items.update', $item->id), [
            'quantity' => 2,
        ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 1,
        ]);
    }

    public function test_customer_can_review_their_checkout(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(stock: 3, price: '12.50');
        $cart = Cart::create(['user_id' => $customer->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($customer)->get(route('checkout.create'));

        $response->assertInertia(
            fn(Assert $page) => $page
                ->component('Checkout')
                ->where('cart.itemCount', 2)
                ->where('cart.subtotal', '25.00')
                ->where('cart.items.0.name', $product->name)
        );
    }

    public function test_checkout_creates_order_decrements_stock_and_clears_cart(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(stock: 5, price: '12.50');
        $cart = Cart::create(['user_id' => $customer->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($customer)->post(route('checkout.store'));

        $response->assertRedirectToRoute('shop.index');
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'total_amount' => '25.00',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price_at_purchase' => '12.50',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_rejects_stock_changes_without_creating_an_order(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(stock: 1);
        $cart = Cart::create(['user_id' => $customer->id]);
        $item = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($customer)->post(route('checkout.store'));

        $response->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertModelExists($item);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
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

    private function createProduct(int $stock, string $price = '8.25'): Product
    {
        $category = ProductCategory::create(['name' => 'Pain Relief']);

        return Product::create([
            'product_category_id' => $category->id,
            'name' => 'Paracetamol 500mg',
            'price' => $price,
            'stock' => $stock,
            'expiration_date' => today()->addYear(),
        ]);
    }
}
