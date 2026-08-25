<?php

namespace App\Policies;

use App\Models\MedicationAdministration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicationAdministrationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('medication-administration-access');
    }

    public function view(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-show');
    }

    public function create(User $user): bool
    {
        return $user->can('medication-administration-create');
    }

    public function update(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-update');
    }

    public function delete(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-delete');
    }

    public function restore(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-restore');
    }

    public function forceDelete(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-delete');
    }
}
