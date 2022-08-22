<?php

namespace App\Models\Permissions;

use App\Traits\Audits\Auditable;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(Permission|string $permission): bool
    {
        try {
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
        } catch (Exception $e) {
            return false;
        }
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
}
