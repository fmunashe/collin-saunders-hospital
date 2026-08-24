<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Auth\Access\HandlesAuthorization;

class DoctorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('doctor-access');
    }

    public function view(User $user, Doctor $model): bool
    {
        return $user->can('doctor-show');
    }

    public function create(User $user): bool
    {
        return $user->can('doctor-create');
    }

    public function update(User $user, Doctor $model): bool
    {
        return $user->can('doctor-update');
    }

    public function delete(User $user, Doctor $model): bool
    {
        return $user->can('doctor-delete');
    }

    public function restore(User $user, Doctor $model): bool
    {
        return $user->can('doctor-restore');
    }

    public function forceDelete(User $user, Doctor $model): bool
    {
        return $user->can('doctor-delete');
    }
}
