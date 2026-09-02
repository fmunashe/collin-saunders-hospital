<?php

namespace App\Policies\Concerns;

use App\Enums\AdmissionStatus;
use App\Models\Admission;

/**
 * Shared helpers for policies that must block clinical activity against a
 * discharged (non-admitted) admission.
 */
trait ChecksAdmissionActive
{
    /**
     * Whether the given admission (by id) is currently active/admitted.
     * A null/missing id is treated as "not blocked" so nullable relations
     * (e.g. outpatient prescriptions with no admission) are unaffected.
     */
    protected function admissionIsActive(?string $admissionId): bool
    {
        if (empty($admissionId)) {
            return true;
        }

        $admission = Admission::find($admissionId);

        return $admission !== null && $admission->status === AdmissionStatus::Admitted;
    }

    /**
     * Resolve the parent admission id from the current request when creating a
     * record from an admission's detail page (Nova sends viaResource/viaResourceId),
     * or from the submitted admission_id field.
     */
    protected function requestedAdmissionId(): ?string
    {
        $request = request();

        if ($request->input('viaResource') === 'admissions' && $request->input('viaResourceId')) {
            return $request->input('viaResourceId');
        }

        return $request->input('admission') ?? $request->input('admission_id');
    }

    /**
     * True when the create request targets a discharged admission.
     */
    protected function creatingForDischargedAdmission(): bool
    {
        return ! $this->admissionIsActive($this->requestedAdmissionId());
    }
}
