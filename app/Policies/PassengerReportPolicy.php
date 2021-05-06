<?php

namespace App\Policies;

use App\Models\PassengerReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PassengerReportPolicy
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
        return $user->hasPermission('passenger-report-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return mixed
     */
    public function view(User $user, PassengerReport $passengerReport)
    {
        return $user->hasPermission('passenger-report-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return mixed
     */
    public function update(User $user, PassengerReport $passengerReport)
    {
        return $user->hasPermission('passenger-report-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return mixed
     */
    public function delete(User $user, PassengerReport $passengerReport)
    {
        return $user->hasPermission('passenger-report-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return mixed
     */
    public function restore(User $user, PassengerReport $passengerReport)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PassengerReport  $passengerReport
     * @return mixed
     */
    public function forceDelete(User $user, PassengerReport $passengerReport)
    {
        return false;
    }
}
