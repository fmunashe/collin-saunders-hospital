<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('setting-access');
    }

    public function view(User $user, Setting $model): bool
    {
        return $user->can('setting-show');
    }

    /**
     * Settings are provisioned by seeding, not created ad-hoc in the UI.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Setting $model): bool
    {
        return $user->can('setting-update');
    }

    /**
     * Configuration settings must not be deleted.
     */
    public function delete(User $user, Setting $model): bool
    {
        return false;
    }

    public function restore(User $user, Setting $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Setting $model): bool
    {
        return false;
    }
}
