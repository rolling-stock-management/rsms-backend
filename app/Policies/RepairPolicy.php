<?php

namespace App\Policies;

use App\Models\Repair;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepairPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('repair-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repair  $repair
     * @return mixed
     */
    public function view(User $user, Repair $repair)
    {
        return $user->hasPermission('repair-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('repair-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repair  $repair
     * @return mixed
     */
    public function update(User $user, Repair $repair)
    {
        return $user->hasPermission('repair-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repair  $repair
     * @return mixed
     */
    public function delete(User $user, Repair $repair)
    {
        return $user->hasPermission('repair-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repair  $repair
     * @return mixed
     */
    public function restore(User $user, Repair $repair)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repair  $repair
     * @return mixed
     */
    public function forceDelete(User $user, Repair $repair)
    {
        return false;
    }
}
