<?php

namespace App\Policies;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OwnerPolicy
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
        return $user->hasPermission('owner-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Owner  $owner
     * @return mixed
     */
    public function view(User $user, Owner $owner)
    {
        return $user->hasPermission('owner-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('owner-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Owner  $owner
     * @return mixed
     */
    public function update(User $user, Owner $owner)
    {
        return $user->hasPermission('owner-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Owner  $owner
     * @return mixed
     */
    public function delete(User $user, Owner $owner)
    {
        return $user->hasPermission('owner-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Owner  $owner
     * @return mixed
     */
    public function restore(User $user, Owner $owner)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Owner  $owner
     * @return mixed
     */
    public function forceDelete(User $user, Owner $owner)
    {
        return false;
    }
}
