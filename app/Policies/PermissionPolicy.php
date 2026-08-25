<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('permission-access');
    }

    public function view(User $user, Permission $model): bool
    {
        return $user->can('permission-show');
    }

    public function create(User $user): bool
    {
        return $user->can('permission-create');
    }

    public function update(User $user, Permission $model): bool
    {
        return $user->can('permission-update');
    }

    public function delete(User $user, Permission $model): bool
    {
        return $user->can('permission-delete');
    }

    public function restore(User $user, Permission $model): bool
    {
        return $user->can('permission-restore');
    }

    public function forceDelete(User $user, Permission $model): bool
    {
        return $user->can('permission-delete');
    }
}
