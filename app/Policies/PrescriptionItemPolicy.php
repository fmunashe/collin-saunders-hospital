<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PrescriptionItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrescriptionItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('prescription-item-access');
    }

    public function view(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-show');
    }

    public function create(User $user): bool
    {
        return $user->can('prescription-item-create');
    }

    public function update(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-update');
    }

    public function delete(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-delete');
    }

    public function restore(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-restore');
    }

    public function forceDelete(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-delete');
    }
}
