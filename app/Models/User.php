<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Audits\Auditable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Permissions\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Traits\Permissions\HasPermissions;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasPermissions, HasRoles, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['superadmin', 'admin']);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function payees(): HasMany
    {
        return $this->hasMany(Payee::class);
    }

    public function recurring_transactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    public function group_transactions(): HasMany
    {
        return $this->hasMany(GroupTransaction::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected function all_transactions(): Attribute
    {
        return Attribute::make(
            get: fn () => array_merge($this->transactions, $this->group_transactions),
        );
    }


    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email
        ], false));;

        $this->notify(new ResetPasswordNotification($url, $this->first_name));
    }
}
