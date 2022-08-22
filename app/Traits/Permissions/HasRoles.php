<?php

namespace App\Traits\Permissions;

use App\Models\Permissions\Role;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(Role|string $role): bool
    {
        try {
            if (! $role instanceof Role) {
                $role = Role::whereName($role)->first();
            }

            if (! $role) {
                return false;
            }

            foreach ($this->roles as $rel_role) {
                if ($rel_role->id === $role->id) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if (! $role instanceof Role) {
                $role = Role::whereName($role)->first();
            }

            if (! $role) {
                continue;
            }

            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if (! $role instanceof Role) {
                $role = Role::whereName($role)->first();
            }

            if (! $role) {
                return false;
            }

            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function assignRole(Role|string $role): void
    {
        if (! $role instanceof Role) {
            $role = Role::whereName($role)->first();
        }

        if ($role) {
            if (! $this->hasRole($role)) {
                $this->roles()->attach([$role->id]);
            }
        }
    }

    public function assignRoles(array $roles): void
    {
        foreach ($roles as $role) {
            if (! $role instanceof Role) {
                $role = Role::whereName($role)->first();
            }

            if (! $role) {
                continue;
            }

            if (! $this->hasRole($role)) {
                $this->roles()->attach([$role->id]);
            }
        }
    }

    public function removeRole(Role|string $role): void
    {
        if (! $role instanceof Role) {
            $role = Role::whereName($role)->first();
        }

        if ($role) {
            if ($this->hasRole($role)) {
                $this->roles()->detach([$role->id]);
            }
        }
    }

    public function removeRoles(array $roles): void
    {
        foreach ($roles as $role) {
            if (! $role instanceof Role) {
                $role = Role::whereName($role)->first();
            }

            if (! $role) {
                continue;
            }

            if ($this->hasRole($role)) {
                $this->roles()->detach([$role->id]);
            }
        }
    }

    public function removeAllRoles(): void
    {
        foreach ($this->roles as $role) {
            $this->roles()->detach([$role->id]);
        }
    }
}
