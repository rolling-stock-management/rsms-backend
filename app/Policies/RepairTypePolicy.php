<?php

namespace App\Policies;

use App\Models\RepairType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepairTypePolicy
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
        return $user->hasPermission('repair-type-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairType  $repairType
     * @return mixed
     */
    public function view(User $user, RepairType $repairType)
    {
        return $user->hasPermission('repair-type-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('repair-type-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairType  $repairType
     * @return mixed
     */
    public function update(User $user, RepairType $repairType)
    {
        return $user->hasPermission('repair-type-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairType  $repairType
     * @return mixed
     */
    public function delete(User $user, RepairType $repairType)
    {
        return $user->hasPermission('repair-type-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairType  $repairType
     * @return mixed
     */
    public function restore(User $user, RepairType $repairType)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairType  $repairType
     * @return mixed
     */
    public function forceDelete(User $user, RepairType $repairType)
    {
        return false;
    }
}
