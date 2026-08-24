<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('department-access');
    }

    public function view(User $user, Department $model): bool
    {
        return $user->can('department-show');
    }

    public function create(User $user): bool
    {
        return $user->can('department-create');
    }

    public function update(User $user, Department $model): bool
    {
        return $user->can('department-update');
    }

    public function delete(User $user, Department $model): bool
    {
        return $user->can('department-delete');
    }

    public function restore(User $user, Department $model): bool
    {
        return $user->can('department-restore');
    }

    public function forceDelete(User $user, Department $model): bool
    {
        return $user->can('department-delete');
    }
}
