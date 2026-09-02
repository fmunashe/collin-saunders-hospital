<?php

namespace App\Rules;

use App\Models\Patient;
use App\Models\Ward;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that the selected ward can admit the patient based on gender.
 *
 * A male patient can never be admitted to a female-only (or maternity) ward,
 * and vice-versa. The patient id is resolved from the admission form request.
 */
class WardAcceptsPatientGender implements ValidationRule
{
    public function __construct(private ?string $patientId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $patientId = $this->patientId ?: request()->input('patient');

        if (empty($patientId)) {
            return; // patient validated separately
        }

        $ward = Ward::find($value);
        $patient = Patient::find($patientId);

        if (! $ward || ! $patient) {
            return;
        }

        if (! $ward->acceptsGender($patient->gender)) {
            $restriction = $ward->effectiveGenderRestriction();
            $label = $restriction ? ucfirst($restriction->value) : 'restricted';

            $fail("This ward is {$label}-only and cannot admit a {$patient->gender->value} patient.");
        }
    }
}
