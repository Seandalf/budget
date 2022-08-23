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
        $data = [
            ['name' => 'superadmin'],
            ['name' => 'admin'],
            ['name' => 'free-customer'],
            ['name' => 'suspended-customer'],
        ];

        Role::upsert($data, 'name');

        $roles = Role::all();

        foreach ($roles as $role) {
            switch ($role->name) {
                case 'superadmin':
                    $permissions = Permission::all();
                    foreach ($permissions as $permission) {
                        $role->assignPermission($permission);
                    }
                    break;
                case 'admin':
                    $role->assignPermissions([   
                        'create-user',
                        'update-user',
                        'delete-user',
                        'view-user',
                        'view-any-user',

                        'create-permission',
                        'update-permission',
                        'delete-permission',
                        'view-permission',
                        'view-any-permission',
                        'assign-permission',
                        'remove-permission',

                        'create-role',
                        'update-role',
                        'delete-role',
                        'view-role',
                        'view-any-role',
                        'assign-role',
                        'remove-role',

                        'create-budget',
                        'update-budget',
                        'delete-budget',
                        'view-budget',
                        'view-any-budget',
                        'restore-budget',

                        'create-category',
                        'update-category',
                        'delete-category',
                        'view-category',
                        'view-any-category',
                        'restore-category',

                        'create-currency',
                        'update-currency',
                        'delete-currency',
                        'view-currency',
                        'view-any-currency',
                        'restore-currency',

                        'create-gtransaction',
                        'update-gtransaction',
                        'delete-gtransaction',
                        'view-gtransaction',
                        'view-any-gtransaction',
                        'restore-gtransaction',

                        'create-interval',
                        'update-interval',
                        'delete-interval',
                        'view-interval',
                        'view-any-interval',
                        'restore-interval',

                        'create-payee',
                        'update-payee',
                        'delete-payee',
                        'view-payee',
                        'view-any-payee',
                        'restore-payee',

                        'create-rtransaction',
                        'update-rtransaction',
                        'delete-rtransaction',
                        'view-rtransaction',
                        'view-any-rtransaction',
                        'restore-rtransaction',

                        'create-transaction',
                        'update-transaction',
                        'delete-transaction',
                        'view-transaction',
                        'view-any-transaction',
                        'restore-transaction',
                    ]);
                    break;
                case 'free customer':
                    $role->assignPermissions([
                        'create-budget',
                        'update-budget',
                        'delete-budget',
                        'view-budget',
                        'restore-budget',
                        'budget-9',

                        'create-category',
                        'update-category',
                        'delete-category',
                        'view-category',
                        'restore-category',

                        'create-gtransaction',
                        'update-gtransaction',
                        'delete-gtransaction',
                        'view-gtransaction',
                        'restore-gtransaction',

                        'create-interval',
                        'update-interval',
                        'delete-interval',
                        'view-interval',
                        'restore-interval',

                        'create-payee',
                        'update-payee',
                        'delete-payee',
                        'view-payee',
                        'restore-payee',

                        'create-rtransaction',
                        'update-rtransaction',
                        'delete-rtransaction',
                        'view-rtransaction',
                        'restore-rtransaction',

                        'create-transaction',
                        'update-transaction',
                        'delete-transaction',
                        'view-transaction',
                        'restore-transaction',
                    ]);
                    break;
            }
        }
    }
}
