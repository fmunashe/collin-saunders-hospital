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

    /**
     * Prescriptions are a clinical record and must never be deleted.
     * Cancel a prescription via its status instead of removing it.
     */
    public function delete(User $user, Prescription $model): bool
    {
        return false;
    }

    public function restore(User $user, Prescription $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Prescription $model): bool
    {
        return false;
    }
}
