<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_product_management(): void
    {
        $this->get(route('products.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_non_admin_cannot_access_product_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $this->actingAs($user)->get(route('products.index'))
            ->assertForbidden();
    }

    public function test_seeded_user_can_view_products_but_cannot_create_them(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole(Role::findByName('user', 'web'));

        $this->actingAs($user)->get(route('products.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Products/Index')
                    ->where('auth.user.roles.0', 'user')
                    ->where('auth.user.permissions.0', 'view products')
            );

        $this->actingAs($user)->get(route('products.create'))
            ->assertForbidden();

        $this->actingAs($user)->post(route('products.store'), [])
            ->assertForbidden();
    }

    public function test_seeded_manager_can_create_and_edit_products_but_cannot_delete_them(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = User::factory()->create();
        $manager->assignRole(Role::findByName('manager', 'web'));
        $category = ProductCategory::create(['name' => 'Medicine']);
        $product = Product::create([
            'product_category_id' => $category->id,
            'name' => 'Sample Product',
            'price' => '12.50',
            'stock' => 5,
        ]);

        $this->actingAs($manager)->get(route('products.create'))
            ->assertInertia(fn (Assert $page) => $page->component('Products/Form'));

        $this->actingAs($manager)->get(route('products.edit', $product))
            ->assertInertia(fn (Assert $page) => $page->component('Products/Form'));

        $this->actingAs($manager)->delete(route('products.destroy', $product))
            ->assertForbidden();

        $this->assertModelExists($product);
    }

    public function test_product_create_page_renders_form_component_with_category_options(): void
    {
        $admin = $this->createAdmin();
        $category = ProductCategory::create([
            'name' => 'Medicine',
        ]);

        $response = $this->actingAs($admin)->get(route('products.create'));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Products/Form')
                ->where('item', null)
                ->has('categories', 1)
                ->where('categories.0.id', $category->id)
                ->has('suppliers', 0)
        );
    }

    public function test_product_index_show_and_edit_pages_render_inertia_components(): void
    {
        $admin = $this->createAdmin();
        $category = ProductCategory::create(['name' => 'Medicine']);
        $product = Product::create([
            'product_category_id' => $category->id,
            'name' => 'Sample Product',
            'price' => '12.50',
            'stock' => 5,
        ]);

        $this->actingAs($admin)->get(route('products.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Products/Index')
                    ->has('products', 1)
                    ->where('products.0.name', $product->name)
            );

        $this->actingAs($admin)->get(route('products.show', $product))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Products/Show')
                    ->where('item.id', $product->id)
                    ->where('item.category.name', $category->name)
            );

        $this->actingAs($admin)->get(route('products.edit', $product))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Products/Form')
                    ->where('item.id', $product->id)
                    ->has('categories', 1)
                    ->has('suppliers', 0)
            );
    }

    public function test_valid_product_submission_creates_the_product_and_redirects(): void
    {
        $admin = $this->createAdmin();
        $category = ProductCategory::create([
            'name' => 'Medicine',
        ]);
        $supplier = Supplier::create([
            'name' => 'Sample Supplier',
        ]);

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'product_category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Sample Product',
            'description' => 'Product description',
            'dosage' => '500 mg',
            'strength' => 'Capsule',
            'requires_prescription' => true,
            'price' => '12.50',
            'stock' => 5,
            'expiration_date' => null,
        ]);

        $response->assertRedirectToRoute('products.index');
        $response->assertSessionHas('success', 'Product created successfully');
        $this->assertDatabaseHas('products', [
            'product_category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Sample Product',
            'description' => 'Product description',
            'dosage' => '500 mg',
            'strength' => 'Capsule',
            'requires_prescription' => true,
            'price' => '12.50',
            'stock' => 5,
        ]);
    }

    private function createAdmin(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('admin', 'web'));

        return $admin;
    }
}
