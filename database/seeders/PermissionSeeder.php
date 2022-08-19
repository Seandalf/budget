<?php

namespace Database\Seeders;

use App\Models\Permissions\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // User management
            ['name' => 'create-user'],
            ['name' => 'update-user'],
            ['name' => 'delete-user'],
            ['name' => 'show-user'],
            ['name' => 'show-users'],
            ['name' => 'impersonate-user'],

            // Budgets
            ['name' => 'create-budget'],
            ['name' => 'update-budget'],
            ['name' => 'delete-budget'],
            ['name' => 'show-budget'],
            ['name' => 'show-budgets'],
            ['name' => 'multiple-budgets'],
            ['name' => 'budget-9'],
            ['name' => 'budget-12'],
            ['name' => 'budget-unlimited'],

            // Permission system
            ['name' => 'create-permission'],
            ['name' => 'update-permission'],
            ['name' => 'delete-permissions'],
            ['name' => 'show-permissions'],
            ['name' => 'assign-permissions'],
            ['name' => 'remove-permissions'],
            ['name' => 'create-role'],
            ['name' => 'update-role'],
            ['name' => 'delete-roles'],
            ['name' => 'show-roles'],
            ['name' => 'assign-roles'],
            ['name' => 'remove-roles'],
        ];

        Permission::upsert($data, 'name');
    }
}
