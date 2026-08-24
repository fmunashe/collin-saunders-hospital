<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ward;
use Illuminate\Auth\Access\HandlesAuthorization;

class WardPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ward-access');
    }

    public function view(User $user, Ward $model): bool
    {
        return $user->can('ward-show');
    }

    public function create(User $user): bool
    {
        return $user->can('ward-create');
    }

    public function update(User $user, Ward $model): bool
    {
        return $user->can('ward-update');
    }

    public function delete(User $user, Ward $model): bool
    {
        return $user->can('ward-delete');
    }

    public function restore(User $user, Ward $model): bool
    {
        return $user->can('ward-restore');
    }

    public function forceDelete(User $user, Ward $model): bool
    {
        return $user->can('ward-delete');
    }
}
