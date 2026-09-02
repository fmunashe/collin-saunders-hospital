<?php

namespace App\Policies;

use App\Models\MedicationAdministration;
use App\Models\User;
use App\Policies\Concerns\ChecksAdmissionActive;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicationAdministrationPolicy
{
    use ChecksAdmissionActive;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('medication-administration-access');
    }

    public function view(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-show');
    }

    public function create(User $user): bool
    {
        if (! $user->can('medication-administration-create')) {
            return false;
        }

        // Cannot record administrations against a discharged patient.
        return ! $this->creatingForDischargedAdmission();
    }

    public function update(User $user, MedicationAdministration $model): bool
    {
        if (! $user->can('medication-administration-update')) {
            return false;
        }

        return $this->admissionIsActive($model->admission_id);
    }

    public function delete(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-delete');
    }

    public function restore(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-restore');
    }

    public function forceDelete(User $user, MedicationAdministration $model): bool
    {
        return $user->can('medication-administration-delete');
    }
}
