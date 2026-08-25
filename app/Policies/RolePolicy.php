<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('role-access');
    }

    public function view(User $user, Role $model): bool
    {
        return $user->can('role-show');
    }

    public function create(User $user): bool
    {
        return $user->can('role-create');
    }

    public function update(User $user, Role $model): bool
    {
        return $user->can('role-update');
    }

    public function delete(User $user, Role $model): bool
    {
        // Prevent deleting admin role
        if ($model->name === 'admin') {
            return false;
        }

        return $user->can('role-delete');
    }

    public function restore(User $user, Role $model): bool
    {
        return $user->can('role-restore');
    }

    public function forceDelete(User $user, Role $model): bool
    {
        return $user->can('role-delete');
    }
}
