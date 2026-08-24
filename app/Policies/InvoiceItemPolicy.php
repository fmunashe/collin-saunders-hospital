<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InvoiceItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('invoice-item-access');
    }

    public function view(User $user, InvoiceItem $model): bool
    {
        return $user->can('invoice-item-show');
    }

    public function create(User $user): bool
    {
        return $user->can('invoice-item-create');
    }

    public function update(User $user, InvoiceItem $model): bool
    {
        return $user->can('invoice-item-update');
    }

    public function delete(User $user, InvoiceItem $model): bool
    {
        return $user->can('invoice-item-delete');
    }

    public function restore(User $user, InvoiceItem $model): bool
    {
        return $user->can('invoice-item-restore');
    }

    public function forceDelete(User $user, InvoiceItem $model): bool
    {
        return $user->can('invoice-item-delete');
    }
}
