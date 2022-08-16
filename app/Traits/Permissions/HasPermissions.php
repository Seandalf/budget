<?php

namespace App\Traits\Permissions;

use App\Models\Permissions\Permission;
use Exception;
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
        if (!$permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if (!$permission) {
            return false;
        }

        foreach ($this->permissions as $perm) {
            if ($perm->id === $permission->id) {
                return true;
            }
        }

        return false;
    }

    public function hasPermissionViaRole(Permission|string $permission): bool
    {
        if (!$this->roles) {
            return false;
        }

        if (!$permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if (!$permission) {
            return false;
        }

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $perm) {
                if ($perm->id === $permission->id) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!is_string($permission)) {
                continue;
            }

            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!is_string($permission)) {
                return false;
            }

            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function getAllPermissions(): array
    {
        $permissions = $this->permissions;
        $role_permissions = $this->roles ? $this->roles->permissions : [];

        return array_merge($permissions, $role_permissions);
    }

    public function getPermissionsViaRoles(): array
    {
        if (!$this->roles) {
            return [];
        }

        return $this->roles->permissions;
    }

    public function assignPermission(Permission|string $permission): void
    {
        if (!$permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if (!$this->hasPermission($permission)) {
            $this->permissions->attach([$permission->id]);
        }
    }

    public function assignPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                if (!$permission instanceof Permission) {
                    $permission = Permission::whereName($permission)->first();
                }
                $this->permissions->attach([$permission->id]);
            }
        }
    }

    public function removePermission(Permission|string $permission): void
    {
        if (!$permission instanceof Permission) {
            $permission = Permission::whereName($permission)->first();
        }

        if ($this->hasPermission($permission)) {
            $this->permissions->detach([$permission->id]);
        }
    }

    public function removePermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                if (!$permission instanceof Permission) {
                    $permission = Permission::whereName($permission)->first();
                }
                $this->permissions->detach([$permission->id]);
            }
        }
    }

    public function removeAllPermissions(): void
    {
        foreach ($this->permissions as $permission) {
            $this->permissions->detach([$permission->id]);
        }
    }
}