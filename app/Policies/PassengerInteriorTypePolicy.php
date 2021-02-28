<?php

namespace App\Policies;

use App\Models\PassengerInteriorType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PassengerInteriorTypePolicy
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
        return $user->hasPermission('passenger-interior-type-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return mixed
     */
    public function view(User $user, PassengerInteriorType $passengerInteriorType)
    {
        return $user->hasPermission('passenger-interior-type-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('passenger-interior-type-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return mixed
     */
    public function update(User $user, PassengerInteriorType $passengerInteriorType)
    {
        return $user->hasPermission('passenger-interior-type-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return mixed
     */
    public function delete(User $user, PassengerInteriorType $passengerInteriorType)
    {
        return $user->hasPermission('passenger-interior-type-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return mixed
     */
    public function restore(User $user, PassengerInteriorType $passengerInteriorType)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerInteriorType  $passengerInteriorType
     * @return mixed
     */
    public function forceDelete(User $user, PassengerInteriorType $passengerInteriorType)
    {
        return false;
    }
}
