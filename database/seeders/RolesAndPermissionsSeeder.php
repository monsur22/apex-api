<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create  permissions
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
        
        $adminRole->permissions()->attach(Permission::whereIn('name', [
            'product-list', 'product-create', 'product-edit', 'product-delete'
        ])->pluck('id'));
    
        $customerRole->permissions()->attach(Permission::whereIn('name', [
            'create-orders', 'view-own-orders'
        ])->pluck('id'));
    }
}
