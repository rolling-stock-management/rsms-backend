<?php

namespace App\Policies;

use App\Models\Timetable;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimetablePolicy
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
        return $user->hasPermission('timetable-viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Timetable  $timetable
     * @return mixed
     */
    public function view(User $user, Timetable $timetable)
    {
        return $user->hasPermission('timetable-view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('timetable-create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Timetable  $timetable
     * @return mixed
     */
    public function update(User $user, Timetable $timetable)
    {
        return $user->hasPermission('timetable-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Timetable  $timetable
     * @return mixed
     */
    public function delete(User $user, Timetable $timetable)
    {
        return $user->hasPermission('timetable-delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Timetable  $timetable
     * @return mixed
     */
    public function restore(User $user, Timetable $timetable)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Timetable  $timetable
     * @return mixed
     */
    public function forceDelete(User $user, Timetable $timetable)
    {
        return false;
    }
}
