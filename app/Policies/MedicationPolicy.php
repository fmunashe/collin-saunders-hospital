<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Medication;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('medication-access');
    }

    public function view(User $user, Medication $model): bool
    {
        return $user->can('medication-show');
    }

    public function create(User $user): bool
    {
        return $user->can('medication-create');
    }

    public function update(User $user, Medication $model): bool
    {
        return $user->can('medication-update');
    }

    public function delete(User $user, Medication $model): bool
    {
        return $user->can('medication-delete');
    }

    public function restore(User $user, Medication $model): bool
    {
        return $user->can('medication-restore');
    }

    public function forceDelete(User $user, Medication $model): bool
    {
        return $user->can('medication-delete');
    }
}
