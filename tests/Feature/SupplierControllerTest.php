<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
