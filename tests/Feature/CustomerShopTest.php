<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('shop.index'));

        $response->assertRedirectToRoute('login');
    }

    public function test_admin_cannot_view_the_customer_shop(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($admin)->get(route('shop.index'));

        $response->assertForbidden();
    }

    public function test_customer_can_filter_products_including_sold_out_items(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $category = ProductCategory::create(['name' => 'Vitamins']);
        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Vitamin C',
            'description' => 'Daily immune support',
            'price' => '12.50',
            'stock' => 4,
        ]);
        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Vitamin D',
            'price' => '9.50',
            'stock' => 0,
        ]);
        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Expired Vitamin',
            'price' => '8.50',
            'stock' => 5,
            'expiration_date' => today()->subDay(),
        ]);

        $response = $this->actingAs($customer)->get(route('shop.index', [
            'search' => 'Vitamin',
            'category' => $category->id,
            'sort' => 'price_asc',
        ]));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Shop')
                ->where('customerName', $customer->name)
                ->where('filters.search', 'Vitamin')
                ->where('filters.sort', 'price_asc')
                ->where('filters.availability', 'all')
                ->where('products.total', 2)
                ->where('products.data.0.name', 'Vitamin D')
                ->where('products.data.0.stock', 0)
                ->where('products.data.0.category.name', 'Vitamins')
                ->where('products.data.1.name', 'Vitamin C')
        );
    }

    public function test_customer_can_filter_products_that_are_in_stock(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $category = ProductCategory::create(['name' => 'Vitamins']);
        $availableProduct = Product::create([
            'product_category_id' => $category->id,
            'name' => 'Available Vitamin',
            'price' => '12.50',
            'stock' => 4,
        ]);
        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Sold Out Vitamin',
            'price' => '9.50',
            'stock' => 0,
        ]);

        $this->actingAs($customer)
            ->get(route('shop.index', ['availability' => 'in_stock']))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Shop')
                ->where('filters.availability', 'in_stock')
                ->where('products.total', 1)
                ->where('products.data.0.id', $availableProduct->id));
    }

    public function test_customer_can_filter_products_that_are_sold_out(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $category = ProductCategory::create(['name' => 'Vitamins']);
        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Available Vitamin',
            'price' => '12.50',
            'stock' => 4,
        ]);
        $soldOutProduct = Product::create([
            'product_category_id' => $category->id,
            'name' => 'Sold Out Vitamin',
            'price' => '9.50',
            'stock' => 0,
        ]);

        $this->actingAs($customer)
            ->get(route('shop.index', ['availability' => 'sold_out']))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Shop')
                ->where('filters.availability', 'sold_out')
                ->where('products.total', 1)
                ->where('products.data.0.id', $soldOutProduct->id));
    }

    public function test_registration_assigns_the_customer_role_and_redirects_to_shop(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'New Customer',
            'email' => 'customer@example.test',
            'password' => 'Sensible-Store-2026!',
            'password_confirmation' => 'Sensible-Store-2026!',
        ]);

        $response->assertRedirectToRoute('shop.index');
        $this->assertAuthenticated();
        $this->assertTrue($this->app['auth']->user()->hasRole('user'));
    }

    public function test_customer_login_redirects_to_shop(): void
    {
        $customer = User::factory()->create(['password' => 'Sensible-Store-2026!']);
        $customer->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $response = $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'Sensible-Store-2026!',
        ]);

        $response->assertRedirectToRoute('shop.index');
    }

    public function test_admin_login_still_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create(['password' => 'Sensible-Store-2026!']);
        $admin->assignRole(Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));

        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'Sensible-Store-2026!',
        ]);

        $response->assertRedirectToRoute('dashboard');
    }
}
