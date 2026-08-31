<?php

namespace App\Nova\Filters;

use App\Enums\AdmissionStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class PatientCareStatus extends Filter
{
    public $name = 'Care Status';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        if ($value === 'inpatient') {
            return $query->whereHas('admissions', fn ($q) => $q->where('status', AdmissionStatus::Admitted->value));
        }

        // Outpatient = no active (admitted) admission.
        return $query->whereDoesntHave('admissions', fn ($q) => $q->where('status', AdmissionStatus::Admitted->value));
    }

    public function options(NovaRequest $request): array
    {
        return [
            'Inpatient' => 'inpatient',
            'Outpatient' => 'outpatient',
        ];
    }
}
