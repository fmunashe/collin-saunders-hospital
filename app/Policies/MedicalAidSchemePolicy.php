<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MedicalAidScheme;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalAidSchemePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('medical-aid-scheme-access');
    }

    public function view(User $user, MedicalAidScheme $model): bool
    {
        return $user->can('medical-aid-scheme-show');
    }

    public function create(User $user): bool
    {
        return $user->can('medical-aid-scheme-create');
    }

    public function update(User $user, MedicalAidScheme $model): bool
    {
        return $user->can('medical-aid-scheme-update');
    }

    public function delete(User $user, MedicalAidScheme $model): bool
    {
        return $user->can('medical-aid-scheme-delete');
    }

    public function restore(User $user, MedicalAidScheme $model): bool
    {
        return $user->can('medical-aid-scheme-restore');
    }

    public function forceDelete(User $user, MedicalAidScheme $model): bool
    {
        return $user->can('medical-aid-scheme-delete');
    }
}
