<?php

namespace App\Policies;

use App\Models\Depot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepotPolicy
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
        return $user->hasPermission('depot-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function view(User $user, Depot $depot)
    {
        return $user->hasPermission('depot-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('depot-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function update(User $user, Depot $depot)
    {
        return $user->hasPermission('depot-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function delete(User $user, Depot $depot)
    {
        return $user->hasPermission('depot-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function restore(User $user, Depot $depot)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function forceDelete(User $user, Depot $depot)
    {
        return false;
    }
}
