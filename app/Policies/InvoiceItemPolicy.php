<?php

namespace App\Policies;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
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
        if (! $user->can('invoice-item-create')) {
            return false;
        }

        // Check if the parent invoice is paid via the request
        $request = request();
        $invoiceId = $request->input('viaResourceId') ?? $request->route('resourceId');

        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);

            if ($invoice && $invoice->status === InvoiceStatus::Paid) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, InvoiceItem $model): bool
    {
        if (! $user->can('invoice-item-update')) {
            return false;
        }

        return $model->invoice->status !== InvoiceStatus::Paid;
    }

    public function delete(User $user, InvoiceItem $model): bool
    {
        if (! $user->can('invoice-item-delete')) {
            return false;
        }

        return $model->invoice->status !== InvoiceStatus::Paid;
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
