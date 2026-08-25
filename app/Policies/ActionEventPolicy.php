<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Actions\ActionEvent as ActionEventModel;

class ActionEventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('action-event-access');
    }

    public function view(User $user, ActionEventModel $model): bool
    {
        return $user->can('action-event-show');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ActionEventModel $model): bool
    {
        return false;
    }

    public function delete(User $user, ActionEventModel $model): bool
    {
        return $user->can('action-event-delete');
    }

    public function restore(User $user, ActionEventModel $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, ActionEventModel $model): bool
    {
        return false;
    }
}
