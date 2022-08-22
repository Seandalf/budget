<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Policies\Permissions\RolePolicy;
use App\Models\Permissions\Role;
use App\Policies\Permissions\PermissionPolicy;
use App\Models\Permissions\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Permission::class => PermissionPolicy::class,
        Role::class       => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
