<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirectToRoute('login');
    }

    public function test_non_admin_cannot_view_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertForbidden();
    }

    public function test_admin_receives_dashboard_and_summary_props(): void
    {
        Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole(Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]));

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('adminName', $admin->name)
                ->where('stats.products', 0)
                ->where('stats.orders', 0)
                ->where('stats.customers', 0)
                ->has('alerts', 0)
        );
    }

    public function test_manager_can_view_the_admin_dashboard(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = User::factory()->create();
        $manager->assignRole(Role::findByName('manager', 'web'));

        $this->actingAs($manager)->get('/dashboard')
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Dashboard')
                    ->where('adminName', $manager->name)
                    ->where('auth.user.roles.0', 'manager')
            );
    }
}
