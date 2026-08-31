<?php

namespace App\Policies;

use App\Enums\PrescriptionStatus;
use App\Models\Prescription;
use App\Models\User;
use App\Models\PrescriptionItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrescriptionItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('prescription-item-access');
    }

    public function view(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-show');
    }

    public function create(User $user): bool
    {
        if (! $user->can('prescription-item-create')) {
            return false;
        }

        // Once the parent prescription is fully dispensed, no more items may be
        // added to it. Resolve the parent from the current request context.
        $request = request();
        $prescriptionId = $request->input('viaResourceId') ?? $request->route('resourceId');

        if ($prescriptionId) {
            $prescription = Prescription::find($prescriptionId);

            if ($prescription && $prescription->status === PrescriptionStatus::Dispensed) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, PrescriptionItem $model): bool
    {
        if (! $user->can('prescription-item-update')) {
            return false;
        }

        return $model->prescription->status !== PrescriptionStatus::Dispensed;
    }

    public function delete(User $user, PrescriptionItem $model): bool
    {
        if (! $user->can('prescription-item-delete')) {
            return false;
        }

        return $model->prescription->status !== PrescriptionStatus::Dispensed;
    }

    public function restore(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-restore');
    }

    public function forceDelete(User $user, PrescriptionItem $model): bool
    {
        return $user->can('prescription-item-delete');
    }
}
