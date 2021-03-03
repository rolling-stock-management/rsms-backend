<?php

namespace App\Policies;

use App\Models\RepairWorkshop;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepairWorkshopPolicy
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
        return $user->hasPermission('repair-workshop-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return mixed
     */
    public function view(User $user, RepairWorkshop $repairWorkshop)
    {
        return $user->hasPermission('repair-workshop-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('repair-workshop-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return mixed
     */
    public function update(User $user, RepairWorkshop $repairWorkshop)
    {
        return $user->hasPermission('repair-workshop-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return mixed
     */
    public function delete(User $user, RepairWorkshop $repairWorkshop)
    {
        return $user->hasPermission('repair-workshop-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return mixed
     */
    public function restore(User $user, RepairWorkshop $repairWorkshop)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RepairWorkshop  $repairWorkshop
     * @return mixed
     */
    public function forceDelete(User $user, RepairWorkshop $repairWorkshop)
    {
        return false;
    }
}
