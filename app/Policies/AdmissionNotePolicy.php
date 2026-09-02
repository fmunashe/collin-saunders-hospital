<?php

namespace App\Policies;

use App\Models\AdmissionNote;
use App\Models\User;
use App\Policies\Concerns\ChecksAdmissionActive;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdmissionNotePolicy
{
    use ChecksAdmissionActive;
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('admission-note-access');
    }

    public function view(User $user, AdmissionNote $model): bool
    {
        return $user->can('admission-note-show');
    }

    public function create(User $user): bool
    {
        if (! $user->can('admission-note-create')) {
            return false;
        }

        // Cannot add notes to a discharged patient's admission.
        return ! $this->creatingForDischargedAdmission();
    }

    public function update(User $user, AdmissionNote $model): bool
    {
        if (! $user->can('admission-note-update')) {
            return false;
        }

        // No edits once the patient is discharged.
        if (! $this->admissionIsActive($model->admission_id)) {
            return false;
        }

        // Only the author of the note may modify it.
        return $model->author_id === $user->id;
    }

    /**
     * Clinical notes are a permanent record and must not be deleted.
     */
    public function delete(User $user, AdmissionNote $model): bool
    {
        return false;
    }

    public function restore(User $user, AdmissionNote $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, AdmissionNote $model): bool
    {
        return false;
    }
}
