<?php

namespace App\Policies;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReferralPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('referral-access');
    }

    public function view(User $user, Referral $model): bool
    {
        return $user->can('referral-show');
    }

    public function create(User $user): bool
    {
        return $user->can('referral-create');
    }

    public function update(User $user, Referral $model): bool
    {
        return $user->can('referral-update');
    }

    public function delete(User $user, Referral $model): bool
    {
        return $user->can('referral-delete');
    }

    public function restore(User $user, Referral $model): bool
    {
        return $user->can('referral-restore');
    }

    public function forceDelete(User $user, Referral $model): bool
    {
        return $user->can('referral-delete');
    }
}
