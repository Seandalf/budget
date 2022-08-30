<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Budget $budget)
    {
        return $budget->user_id === $user->id && $user->hasPermission('view-budget');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $budget_count = count($user->budgets);

        return $budget_count === 0 ? $user->hasPermission('create-budget') : $user->hasPermission('multiple-budgets');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Budget $budget)
    {
        return $budget->user_id === $user->id && $user->hasPermission('update-budget');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Budget $budget)
    {
        return $budget->user_id === $user->id && $user->hasPermission('delete-budget');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Budget $budget)
    {
        return $budget->user_id === $user->id && $user->hasPermission('restore-budget');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Budget $budget)
    {
        return false;
    }
}
