<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_category_management_pages_render_inertia_components(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));
        $category = ProductCategory::create(['name' => 'Medicine']);

        $this->actingAs($admin)->get(route('product-categories.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('ProductCategories/Index')
                    ->has('categories', 1)
                    ->where('categories.0.name', $category->name)
            );

        $this->actingAs($admin)->get(route('product-categories.create'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('ProductCategories/Form')
                    ->where('item', null)
            );

        $this->actingAs($admin)->get(route('product-categories.show', $category))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('ProductCategories/Show')
                    ->where('item.id', $category->id)
            );

        $this->actingAs($admin)->get(route('product-categories.edit', $category))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('ProductCategories/Form')
                    ->where('item.id', $category->id)
            );
    }
}
