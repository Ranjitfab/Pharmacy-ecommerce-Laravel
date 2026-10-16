<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('notifications.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_customer_cannot_view_inventory_notifications(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $customer = User::factory()->create();
        $customer->assignRole(Role::findByName('user', 'web'));

        $this->actingAs($customer)->get(route('notifications.index'))
            ->assertForbidden();
    }

    public function test_admin_and_manager_can_view_all_inventory_alert_types(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $category = ProductCategory::create(['name' => 'Medicine']);

        $this->createProduct($category->id, [
            'name' => 'Expired tablets',
            'expiration_date' => today()->subDay(),
        ]);
        $this->createProduct($category->id, [
            'name' => 'Soon-to-expire syrup',
            'expiration_date' => today()->addDays(10),
        ]);
        $this->createProduct($category->id, [
            'name' => 'At-threshold tablets',
            'stock' => 4,
            'reorder_threshold' => 4,
        ]);
        $this->createProduct($category->id, [
            'name' => 'Custom reorder syrup',
            'stock' => 8,
            'reorder_threshold' => 10,
        ]);
        $this->createProduct($category->id, [
            'name' => 'Above-threshold capsules',
            'stock' => 4,
            'reorder_threshold' => 3,
        ]);

        foreach (['admin', 'manager'] as $roleName) {
            $user = User::factory()->create();
            $user->assignRole(Role::findByName($roleName, 'web'));

            $this->actingAs($user)->get(route('notifications.index'))
                ->assertInertia(fn(Assert $page) => $page
                    ->component('Notifications/Index')
                    ->where('auth.user.roles.0', $roleName)
                    ->has('alerts', 4)
                    ->where('alerts.0.type', 'expired')
                    ->where('alerts.0.message', 'Expired tablets expired on ' . today()->subDay()->toDateString())
                    ->where('alerts.1.type', 'expiring_soon')
                    ->where('alerts.1.message', 'Soon-to-expire syrup expires on ' . today()->addDays(10)->toDateString())
                    ->where('alerts.2.type', 'low_stock')
                    ->where('alerts.2.message', 'At-threshold tablets has 4 units remaining (reorder threshold: 4)')
                    ->where('alerts.3.message', 'Custom reorder syrup has 8 units remaining (reorder threshold: 10)'));
        }
    }

    /**
     * @param  array{name?: string, stock?: int, reorder_threshold?: int, expiration_date?: string|null}  $attributes
     */
    private function createProduct(int $categoryId, array $attributes): Product
    {
        return Product::create([
            'product_category_id' => $categoryId,
            'name' => $attributes['name'] ?? 'Sample product',
            'price' => '10.00',
            'stock' => $attributes['stock'] ?? 20,
            'reorder_threshold' => $attributes['reorder_threshold'] ?? 5,
            'expiration_date' => $attributes['expiration_date'] ?? null,
        ]);
    }
}
