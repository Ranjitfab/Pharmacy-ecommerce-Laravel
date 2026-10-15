<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_wishlist(): void
    {
        $this->get(route('wishlist.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_admin_cannot_view_the_customer_wishlist(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));

        $this->actingAs($admin)
            ->get(route('wishlist.index'))
            ->assertForbidden();
    }

    public function test_customer_can_save_a_product_and_see_it_in_their_wishlist(): void
    {
        $customer = $this->customer();
        $product = $this->product();

        $this->actingAs($customer)
            ->post(route('wishlist.items.store', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('product_user', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($customer)
            ->get(route('wishlist.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Wishlist')
                ->where('wishlistCount', 1)
                ->where('products.0.id', $product->id));

        $this->actingAs($customer)
            ->get(route('shop.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Shop')
                ->where('wishlistProductIds', [$product->id]));
    }

    public function test_customer_cannot_see_another_customers_wishlist_items(): void
    {
        $owner = $this->customer();
        $otherCustomer = $this->customer();
        $product = $this->product();
        $owner->wishlist()->attach($product);

        $this->actingAs($otherCustomer)
            ->get(route('wishlist.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Wishlist')
                ->where('wishlistCount', 0)
                ->has('products', 0));
    }

    public function test_customer_can_remove_a_product_from_their_wishlist(): void
    {
        $customer = $this->customer();
        $product = $this->product();
        $customer->wishlist()->attach($product);

        $this->actingAs($customer)
            ->delete(route('wishlist.items.destroy', $product))
            ->assertRedirect();

        $this->assertDatabaseMissing('product_user', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);
    }

    private function customer(): User
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        return $customer;
    }

    private function product(): Product
    {
        $category = ProductCategory::create(['name' => 'Vitamins']);

        return Product::create([
            'product_category_id' => $category->id,
            'name' => 'Vitamin C',
            'description' => 'Daily immune support',
            'price' => '12.50',
            'stock' => 5,
        ]);
    }
}
