<?php

namespace App\Rules;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Ensures a referenced admission is still active (patient currently admitted).
 *
 * Clinical activity — care notes, prescriptions, medication administrations —
 * must not be recorded against a discharged (or transferred/deceased) admission.
 */
class AdmissionIsActive implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // nullability is handled by other rules
        }

        $admission = Admission::find($value);

        if (! $admission) {
            $fail('The selected admission could not be found.');

            return;
        }

        if ($admission->status !== AdmissionStatus::Admitted) {
            $fail('This patient has been discharged. Clinical records cannot be added or changed for a discharged admission.');
        }
    }
}
