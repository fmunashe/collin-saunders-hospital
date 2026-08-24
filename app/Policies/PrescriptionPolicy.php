<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Prescription;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrescriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('prescription-access');
    }

    public function view(User $user, Prescription $model): bool
    {
        return $user->can('prescription-show');
    }

    public function create(User $user): bool
    {
        return $user->can('prescription-create');
    }

    public function update(User $user, Prescription $model): bool
    {
        return $user->can('prescription-update');
    }

    public function delete(User $user, Prescription $model): bool
    {
        return $user->can('prescription-delete');
    }

    public function restore(User $user, Prescription $model): bool
    {
        return $user->can('prescription-restore');
    }

    public function forceDelete(User $user, Prescription $model): bool
    {
        return $user->can('prescription-delete');
    }
}
