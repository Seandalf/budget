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
            ['name' => 'create-user'],
            ['name' => 'update-user'],
            ['name' => 'delete-user'],
            ['name' => 'view-user'],
            ['name' => 'view-any-user'],
            ['name' => 'impersonate-user'],

            ['name' => 'create-permission'],
            ['name' => 'update-permission'],
            ['name' => 'delete-permission'],
            ['name' => 'view-permission'],
            ['name' => 'view-any-permission'],
            ['name' => 'assign-permission'],
            ['name' => 'remove-permission'],

            ['name' => 'create-role'],
            ['name' => 'update-role'],
            ['name' => 'delete-role'],
            ['name' => 'view-role'],
            ['name' => 'view-any-role'],
            ['name' => 'assign-role'],
            ['name' => 'remove-role'],

            ['name' => 'create-budget'],
            ['name' => 'update-budget'],
            ['name' => 'delete-budget'],
            ['name' => 'view-budget'],
            ['name' => 'view-any-budget'],
            ['name' => 'restore-budget'],
            ['name' => 'multiple-budgets'],
            ['name' => 'budget-9'],
            ['name' => 'budget-12'],
            ['name' => 'budget-unlimited'],

            ['name' => 'create-category'],
            ['name' => 'update-category'],
            ['name' => 'delete-category'],
            ['name' => 'view-category'],
            ['name' => 'view-any-category'],
            ['name' => 'restore-category'],

            ['name' => 'create-currency'],
            ['name' => 'update-currency'],
            ['name' => 'delete-currency'],
            ['name' => 'view-currency'],
            ['name' => 'view-any-currency'],
            ['name' => 'restore-currency'],

            ['name' => 'create-gtransaction'],
            ['name' => 'update-gtransaction'],
            ['name' => 'delete-gtransaction'],
            ['name' => 'view-gtransaction'],
            ['name' => 'view-any-gtransaction'],
            ['name' => 'restore-gtransaction'],

            ['name' => 'create-interval'],
            ['name' => 'update-interval'],
            ['name' => 'delete-interval'],
            ['name' => 'view-interval'],
            ['name' => 'view-any-interval'],
            ['name' => 'restore-interval'],

            ['name' => 'create-payee'],
            ['name' => 'update-payee'],
            ['name' => 'delete-payee'],
            ['name' => 'view-payee'],
            ['name' => 'view-any-payee'],
            ['name' => 'restore-payee'],

            ['name' => 'create-rtransaction'],
            ['name' => 'update-rtransaction'],
            ['name' => 'delete-rtransaction'],
            ['name' => 'view-rtransaction'],
            ['name' => 'view-any-rtransaction'],
            ['name' => 'restore-rtransaction'],

            ['name' => 'create-transaction'],
            ['name' => 'update-transaction'],
            ['name' => 'delete-transaction'],
            ['name' => 'view-transaction'],
            ['name' => 'view-any-transaction'],
            ['name' => 'restore-transaction'],
        ];

        Permission::upsert($data, 'name');
    }
}
