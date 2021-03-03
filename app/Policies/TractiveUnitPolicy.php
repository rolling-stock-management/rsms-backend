<?php

namespace App\Policies;

use App\Models\TractiveUnit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TractiveUnitPolicy
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
        return $user->hasPermission('tractive-unit-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TractiveUnit  $tractiveUnit
     * @return mixed
     */
    public function view(User $user, TractiveUnit $tractiveUnit)
    {
        return $user->hasPermission('tractive-unit-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('tractive-unit-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TractiveUnit  $tractiveUnit
     * @return mixed
     */
    public function update(User $user, TractiveUnit $tractiveUnit)
    {
        return $user->hasPermission('tractive-unit-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TractiveUnit  $tractiveUnit
     * @return mixed
     */
    public function delete(User $user, TractiveUnit $tractiveUnit)
    {
        return $user->hasPermission('tractive-unit-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TractiveUnit  $tractiveUnit
     * @return mixed
     */
    public function restore(User $user, TractiveUnit $tractiveUnit)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TractiveUnit  $tractiveUnit
     * @return mixed
     */
    public function forceDelete(User $user, TractiveUnit $tractiveUnit)
    {
        return false;
    }
}
