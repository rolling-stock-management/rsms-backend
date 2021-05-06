<?php

namespace App\Policies;

use App\Models\RollingStockTrain;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RollingStockTrainPolicy
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
        return $user->hasPermission('rolling-stock-train-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return mixed
     */
    public function view(User $user, RollingStockTrain $rollingStockTrain)
    {
        return $user->hasPermission('rolling-stock-train-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('rolling-stock-train-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return mixed
     */
    public function update(User $user, RollingStockTrain $rollingStockTrain)
    {
        return $user->hasPermission('rolling-stock-train-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return mixed
     */
    public function delete(User $user, RollingStockTrain $rollingStockTrain)
    {
        return $user->hasPermission('rolling-stock-train-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return mixed
     */
    public function restore(User $user, RollingStockTrain $rollingStockTrain)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RollingStockTrain  $rollingStockTrain
     * @return mixed
     */
    public function forceDelete(User $user, RollingStockTrain $rollingStockTrain)
    {
        return false;
    }
}
