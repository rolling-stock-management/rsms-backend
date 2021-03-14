<?php

namespace App\Policies;

use App\Models\FreightWagon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FreightWagonPolicy
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
        return $user->hasPermission('freight-wagon-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagon  $freightWagon
     * @return mixed
     */
    public function view(User $user, FreightWagon $freightWagon)
    {
        return $user->hasPermission('freight-wagon-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('freight-wagon-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagon  $freightWagon
     * @return mixed
     */
    public function update(User $user, FreightWagon $freightWagon)
    {
        return $user->hasPermission('freight-wagon-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagon  $freightWagon
     * @return mixed
     */
    public function delete(User $user, FreightWagon $freightWagon)
    {
        return $user->hasPermission('freight-wagon-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagon  $freightWagon
     * @return mixed
     */
    public function restore(User $user, FreightWagon $freightWagon)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagon  $freightWagon
     * @return mixed
     */
    public function forceDelete(User $user, FreightWagon $freightWagon)
    {
        return false;
    }
}
