<?php

namespace App\Policies;

use App\Models\Payee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayeePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Payee $payee)
    {
        return $user->hasPermission('view-payee') && ($user->id === $payee->user_id || $payee->id === null);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-payee');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Payee $payee)
    {
        return $payee->user_id === $user->id && $user->hasPermission('update-payee');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Payee $payee)
    {
        return $payee->user_id === $user->id && $user->hasPermission('delete-payee');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Payee $payee)
    {
        return $payee->user_id === $user->id && $user->hasPermission('restore-payee');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payee  $payee
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Payee $payee)
    {
        return false;
    }
}
