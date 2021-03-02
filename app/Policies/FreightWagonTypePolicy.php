<?php

namespace App\Policies;

use App\Models\FreightWagonType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FreightWagonTypePolicy
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
        return $user->hasPermission('freight-wagon-type-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return mixed
     */
    public function view(User $user, FreightWagonType $freightWagonType)
    {
        return $user->hasPermission('freight-wagon-type-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('freight-wagon-type-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return mixed
     */
    public function update(User $user, FreightWagonType $freightWagonType)
    {
        return $user->hasPermission('freight-wagon-type-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return mixed
     */
    public function delete(User $user, FreightWagonType $freightWagonType)
    {
        return $user->hasPermission('freight-wagon-type-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return mixed
     */
    public function restore(User $user, FreightWagonType $freightWagonType)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FreightWagonType  $freightWagonType
     * @return mixed
     */
    public function forceDelete(User $user, FreightWagonType $freightWagonType)
    {
        return false;
    }
}
