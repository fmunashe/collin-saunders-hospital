<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('invoice-access');
    }

    public function view(User $user, Invoice $model): bool
    {
        return $user->can('invoice-show');
    }

    public function create(User $user): bool
    {
        return $user->can('invoice-create');
    }

    public function update(User $user, Invoice $model): bool
    {
        return $user->can('invoice-update');
    }

    public function delete(User $user, Invoice $model): bool
    {
        return $user->can('invoice-delete');
    }

    public function restore(User $user, Invoice $model): bool
    {
        return $user->can('invoice-restore');
    }

    public function forceDelete(User $user, Invoice $model): bool
    {
        return $user->can('invoice-delete');
    }
}
