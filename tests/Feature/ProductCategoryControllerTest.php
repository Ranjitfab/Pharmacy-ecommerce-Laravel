<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_category_management_pages_render_inertia_components(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('admin', 'web'));
        $category = ProductCategory::create(['name' => 'Medicine']);

        $this->actingAs($admin)->get(route('product-categories.index'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('ProductCategories/Index')
                    ->has('categories', 1)
                    ->where('categories.0.name', $category->name)
            );

        $this->actingAs($admin)->get(route('product-categories.create'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('ProductCategories/Form')
                    ->where('item', null)
            );

        $this->actingAs($admin)->get(route('product-categories.show', $category))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('ProductCategories/Show')
                    ->where('item.id', $category->id)
            );

        $this->actingAs($admin)->get(route('product-categories.edit', $category))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('ProductCategories/Form')
                    ->where('item.id', $category->id)
            );
    }

    public function test_manager_can_manage_categories_but_cannot_delete_them(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = User::factory()->create();
        $manager->assignRole(Role::findByName('manager', 'web'));
        $category = ProductCategory::create(['name' => 'Medicine']);

        $this->actingAs($manager)->get(route('product-categories.index'))
            ->assertInertia(fn(Assert $page) => $page
                ->component('ProductCategories/Index')
                ->has('categories', 1));

        $this->actingAs($manager)->get(route('product-categories.create'))
            ->assertInertia(fn(Assert $page) => $page->component('ProductCategories/Form'));

        $this->actingAs($manager)->get(route('product-categories.edit', $category))
            ->assertInertia(fn(Assert $page) => $page->component('ProductCategories/Form'));

        $this->actingAs($manager)->put(route('product-categories.update', $category), [
            'name' => 'Updated Medicine',
            'description' => null,
        ])->assertRedirectToRoute('product-categories.index');

        $this->actingAs($manager)->delete(route('product-categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'Updated Medicine',
        ]);
    }
}
