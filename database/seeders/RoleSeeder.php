<?php

namespace Database\Seeders;

use App\Models\Permissions\Permission;
use App\Models\Permissions\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            ['name' => 'superadmin'],
            ['name' => 'admin'],
            ['name' => 'free-customer'],
        ]);

        $roles = Role::all();

        foreach ($roles as $role) {
            switch ($role->name) {
                case 'superadmin':
                    $permissions = Permission::all();
                    foreach ($permissions as $permission) {
                        $role->assignPermission($permission);
                    }
                    break;
                case 'free customer':
                    $role->assignPermissions([
                        'create-budget', 
                        'update-budget', 
                        'delete-budget', 
                        'view-budget', 
                        'see-9',
                    ]);
            }
        }
    }
}
