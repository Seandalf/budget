<?php

namespace App\Policies;

use App\Models\RecurringTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringTransactionPolicy
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
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, RecurringTransaction $recurringTransaction)
    {
        return $recurringTransaction->user_id === $user->id && $user->hasPermission('view-rtransaction');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-rtransaction');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RecurringTransaction $recurringTransaction)
    {
        return $recurringTransaction->user_id === $user->id && $user->hasPermission('update-rtransaction');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RecurringTransaction $recurringTransaction)
    {
        return $recurringTransaction->user_id === $user->id && $user->hasPermission('delete-rtransaction');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, RecurringTransaction $recurringTransaction)
    {
        return $recurringTransaction->user_id === $user->id && $user->hasPermission('restore-rtransaction');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RecurringTransaction  $recurringTransaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, RecurringTransaction $recurringTransaction)
    {
        return false;
    }
}
