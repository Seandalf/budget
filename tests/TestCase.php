<?php

namespace Tests;

use App\Models\Permissions\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    protected function create_user_with_role($role = 'superadmin', $attributes = []): Authenticatable
    {
        $role = Role::whereName($role)->first();
        $user = User::factory($attributes)->create();
        $user->assignRole($role);

        return User::find($user->id);
    }
}
