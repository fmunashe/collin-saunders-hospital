<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admission;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdmissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('admission-access');
    }

    public function view(User $user, Admission $model): bool
    {
        return $user->can('admission-show');
    }

    public function create(User $user): bool
    {
        return $user->can('admission-create');
    }

    public function update(User $user, Admission $model): bool
    {
        return $user->can('admission-update');
    }

    public function delete(User $user, Admission $model): bool
    {
        return $user->can('admission-delete');
    }

    public function restore(User $user, Admission $model): bool
    {
        return $user->can('admission-restore');
    }

    public function forceDelete(User $user, Admission $model): bool
    {
        return $user->can('admission-delete');
    }
}
