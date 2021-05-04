<?php

namespace App\Policies;

use App\Models\Train;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainPolicy
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
        return $user->hasPermission('train-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Train  $train
     * @return mixed
     */
    public function view(User $user, Train $train)
    {
        return $user->hasPermission('train-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('train-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Train  $train
     * @return mixed
     */
    public function update(User $user, Train $train)
    {
        return $user->hasPermission('train-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Train  $train
     * @return mixed
     */
    public function delete(User $user, Train $train)
    {
        return $user->hasPermission('train-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Train  $train
     * @return mixed
     */
    public function restore(User $user, Train $train)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Train  $train
     * @return mixed
     */
    public function forceDelete(User $user, Train $train)
    {
        return false;
    }
}
