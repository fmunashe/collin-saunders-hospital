<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('staff-access');
    }

    public function view(User $user, Staff $model): bool
    {
        return $user->can('staff-show');
    }

    public function create(User $user): bool
    {
        return $user->can('staff-create');
    }

    public function update(User $user, Staff $model): bool
    {
        return $user->can('staff-update');
    }

    public function delete(User $user, Staff $model): bool
    {
        return $user->can('staff-delete');
    }

    public function restore(User $user, Staff $model): bool
    {
        return $user->can('staff-restore');
    }

    public function forceDelete(User $user, Staff $model): bool
    {
        return $user->can('staff-delete');
    }
}
