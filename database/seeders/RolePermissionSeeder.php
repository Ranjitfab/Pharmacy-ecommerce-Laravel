<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // permissions
        $permissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'manager',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
        ]);

        $admin->givePermissionTo($permissions);

        $manager->givePermissionTo([
            'view products',
            'create products',
            'edit products',
        ]);

        $user->givePermissionTo(
            'view products',
        );
    }
}
