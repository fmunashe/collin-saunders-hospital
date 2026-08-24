<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Bed;
use Illuminate\Auth\Access\HandlesAuthorization;

class BedPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('bed-access');
    }

    public function view(User $user, Bed $model): bool
    {
        return $user->can('bed-show');
    }

    public function create(User $user): bool
    {
        return $user->can('bed-create');
    }

    public function update(User $user, Bed $model): bool
    {
        return $user->can('bed-update');
    }

    public function delete(User $user, Bed $model): bool
    {
        return $user->can('bed-delete');
    }

    public function restore(User $user, Bed $model): bool
    {
        return $user->can('bed-restore');
    }

    public function forceDelete(User $user, Bed $model): bool
    {
        return $user->can('bed-delete');
    }
}
