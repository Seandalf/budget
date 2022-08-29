<?php

namespace App\Traits\Permissions;

use App\Models\Permissions\Permission;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(Permission|string $permission): bool
    {
        try {
            return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasDirectPermission(Permission|string $permission): bool
    {
        if (! $permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if ($permission) {
            foreach ($this->permissions as $perm) {
                if ($perm->id === $permission->id) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasPermissionViaRole(Permission|string $permission): bool
    {
        if (! $this->roles) {
            return false;
        }

        if (! $permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if ($permission) {
            foreach ($this->roles as $role) {
                foreach ($role->permissions as $perm) {
                    if ($perm->id === $permission->id) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $permission instanceof Permission) {
                $permission = Permission::whereName($permission)->first();
            }

            if ($permission && $this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $permission instanceof Permission) {
                $permission = Permission::whereName($permission)->first();
            }

            if (! $permission || ! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function getAllPermissions(): Collection
    {
        $permissions = $this->permissions;
        $roles = $this->roles ?? [];

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return $permissions;
    }

    public function getAllPermissionsAsArray(): array
    {
        $permissions = $this->getAllPermissions();

        $final = [];
        foreach ($permissions as $permission) {
            $final[] = $permission->name;
        }

        return $final;
    }

    public function getPermissionsViaRoles(): array
    {
        if (! $this->roles) {
            return [];
        }

        return $this->roles->permissions;
    }

    public function assignPermission(Permission|string $permission): void
    {
        if (! $permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if ($permission && ! $this->hasPermission($permission)) {
            $this->permissions()->attach([$permission->id]);
        }
    }

    public function assignPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            if (! $permission instanceof Permission) {
                $permission = Permission::whereName($permission)->first();
            }

            if ($permission && ! $this->hasPermission($permission)) {
                $this->permissions()->attach([$permission->id]);
            }
        }
    }

    public function removePermission(Permission|string $permission): void
    {
        if (! $permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if ($permission && $this->hasPermission($permission)) {
            $this->permissions()->detach([$permission->id]);
        }
    }

    public function removePermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            if (! $permission instanceof Permission) {
                $permission = Permission::whereName($permission)->first();
            }

            if ($permission && $this->hasPermission($permission)) {
                $this->permissions()->detach([$permission->id]);
            }
        }
    }

    public function removeAllPermissions(): void
    {
        foreach ($this->permissions as $permission) {
            $this->permissions()->detach([$permission->id]);
        }
    }
}
