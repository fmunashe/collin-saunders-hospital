<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockMovementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('stock-movement-access');
    }

    public function view(User $user, StockMovement $model): bool
    {
        return $user->can('stock-movement-show');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, StockMovement $model): bool
    {
        return false;
    }

    public function delete(User $user, StockMovement $model): bool
    {
        return $user->can('stock-movement-delete');
    }

    public function restore(User $user, StockMovement $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, StockMovement $model): bool
    {
        return false;
    }
}
