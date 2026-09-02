<?php

namespace App\Policies;

use App\Enums\PrescriptionStatus;
use App\Models\User;
use App\Models\Prescription;
use App\Policies\Concerns\ChecksAdmissionActive;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrescriptionPolicy
{
    use ChecksAdmissionActive;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('prescription-access');
    }

    public function view(User $user, Prescription $model): bool
    {
        return $user->can('prescription-show');
    }

    public function create(User $user): bool
    {
        if (! $user->can('prescription-create')) {
            return false;
        }

        // Cannot prescribe against a discharged patient's admission.
        // (Outpatient prescriptions have no admission and are unaffected.)
        return ! $this->creatingForDischargedAdmission();
    }

    public function update(User $user, Prescription $model): bool
    {
        if (! $user->can('prescription-update')) {
            return false;
        }

        // A fully dispensed prescription is locked — no further edits.
        if ($model->status === PrescriptionStatus::Dispensed) {
            return false;
        }

        // No edits once the linked admission is discharged.
        return $this->admissionIsActive($model->admission_id);
    }

    /**
     * Prescriptions are a clinical record and must never be deleted.
     * Cancel a prescription via its status instead of removing it.
     */
    public function delete(User $user, Prescription $model): bool
    {
        return false;
    }

    public function restore(User $user, Prescription $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, Prescription $model): bool
    {
        return false;
    }
}
