<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('visit-access');
    }

    public function view(User $user, Visit $model): bool
    {
        return $user->can('visit-show');
    }

    public function create(User $user): bool
    {
        return $user->can('visit-create');
    }

    public function update(User $user, Visit $model): bool
    {
        return $user->can('visit-update');
    }

    public function delete(User $user, Visit $model): bool
    {
        return $user->can('visit-delete');
    }

    public function restore(User $user, Visit $model): bool
    {
        return $user->can('visit-restore');
    }

    public function forceDelete(User $user, Visit $model): bool
    {
        return $user->can('visit-delete');
    }
}
