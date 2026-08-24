<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MedicalAidDetail;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalAidDetailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('medical-aid-detail-access');
    }

    public function view(User $user, MedicalAidDetail $model): bool
    {
        return $user->can('medical-aid-detail-show');
    }

    public function create(User $user): bool
    {
        return $user->can('medical-aid-detail-create');
    }

    public function update(User $user, MedicalAidDetail $model): bool
    {
        return $user->can('medical-aid-detail-update');
    }

    public function delete(User $user, MedicalAidDetail $model): bool
    {
        return $user->can('medical-aid-detail-delete');
    }

    public function restore(User $user, MedicalAidDetail $model): bool
    {
        return $user->can('medical-aid-detail-restore');
    }

    public function forceDelete(User $user, MedicalAidDetail $model): bool
    {
        return $user->can('medical-aid-detail-delete');
    }
}
