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
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'view orders',
            'update orders',
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
            'view categories',
            'create categories',
            'edit categories',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'view orders',
            'update orders',
        ]);

        $user->givePermissionTo(
            'view products',
        );
    }
}
