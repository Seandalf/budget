<?php

namespace Database\Seeders;

use App\Models\Permissions\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CurrencySeeder::class,
            TimePeriodSeeder::class,
            PayeeSeeder::class,
            CategorySeeder::class,
        ]);

        if (!in_array(config('app.env'), ['production', 'testing'])) {
            $users = User::factory(10)->create();

            $roles = Role::all()->toArray();
            foreach ($users as $user) {
                $selected_role = $roles[rand(0, count($roles) - 1)];
                $user->assignRole($selected_role['name']);
            }
        }
    }
}
