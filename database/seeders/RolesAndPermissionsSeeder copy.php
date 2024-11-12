<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'create-orders',
            'view-orders',
            'view-own-orders'
        ];
    
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $customerRole = Role::create(['name' => 'customer']);
        
        // Assign permissions to roles
        $adminRole->permissions()->attach(Permission::whereIn('name', [
            'product-list', 'product-create', 'product-edit', 'product-delete', 
            'create-orders', 'view-orders'
        ])->pluck('id'));
    
        $customerRole->permissions()->attach(Permission::whereIn('name', [
            'create-orders', 'view-own-orders'
        ])->pluck('id'));
    }
    
}
