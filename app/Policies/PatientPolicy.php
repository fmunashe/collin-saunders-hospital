<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('patient-access');
    }

    public function view(User $user, Patient $model): bool
    {
        return $user->can('patient-show');
    }

    public function create(User $user): bool
    {
        return $user->can('patient-create');
    }

    public function update(User $user, Patient $model): bool
    {
        return $user->can('patient-update');
    }

    public function delete(User $user, Patient $model): bool
    {
        return $user->can('patient-delete');
    }

    public function restore(User $user, Patient $model): bool
    {
        return $user->can('patient-restore');
    }

    public function forceDelete(User $user, Patient $model): bool
    {
        return $user->can('patient-delete');
    }
}
