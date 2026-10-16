<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('suppliers.index'));

        $response->assertRedirectToRoute('login');
    }

    public function test_non_admin_cannot_access_supplier_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($user)->get(route('suppliers.index'));

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_access_category_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($user)->get(route('product-categories.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_a_supplier(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($admin)->post(route('suppliers.store'), [
            'name' => 'Northstar Medical',
            'contact_person' => 'Alex Lee',
            'email' => 'alex@northstar.test',
            'phone' => '555-0100',
            'address' => '10 Market Road',
        ]);

        $response->assertRedirectToRoute('suppliers.index');
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Northstar Medical',
            'contact_person' => 'Alex Lee',
            'email' => 'alex@northstar.test',
        ]);
    }

    public function test_admin_supplier_management_pages_render_inertia_components(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));
        $supplier = Supplier::create(['name' => 'Northstar Medical']);

        $this->actingAs($admin)->get(route('suppliers.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Suppliers/Index')
                    ->has('suppliers', 1)
                    ->where('suppliers.0.name', $supplier->name)
            );

        $this->actingAs($admin)->get(route('suppliers.create'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Suppliers/Form')
                    ->where('item', null)
            );

        $this->actingAs($admin)->get(route('suppliers.show', $supplier))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Suppliers/Show')
                    ->where('item.id', $supplier->id)
            );

        $this->actingAs($admin)->get(route('suppliers.edit', $supplier))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Suppliers/Form')
                    ->where('item.id', $supplier->id)
            );
    }

    public function test_manager_can_manage_suppliers_but_cannot_delete_them(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = User::factory()->create();
        $manager->assignRole(Role::findByName('manager', 'web'));
        $supplier = Supplier::create(['name' => 'Northstar Medical']);

        $this->actingAs($manager)->get(route('suppliers.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Suppliers/Index')
                ->has('suppliers', 1));

        $this->actingAs($manager)->get(route('suppliers.create'))
            ->assertInertia(fn (Assert $page) => $page->component('Suppliers/Form'));

        $this->actingAs($manager)->put(route('suppliers.update', $supplier), [
            'name' => 'Updated Medical Supply',
            'contact_person' => null,
            'email' => null,
            'phone' => null,
            'address' => null,
        ])->assertRedirectToRoute('suppliers.index');

        $this->actingAs($manager)->delete(route('suppliers.destroy', $supplier))
            ->assertForbidden();

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Medical Supply',
        ]);
    }
}
