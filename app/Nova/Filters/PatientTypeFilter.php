<?php

namespace App\Nova\Filters;

use App\Enums\PatientType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class PatientTypeFilter extends Filter
{
    public $name = 'Patient Type';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('patient_type', $value);
    }

    public function options(NovaRequest $request): array
    {
        return collect(PatientType::cases())
            ->mapWithKeys(fn ($t) => [str_replace('_', ' ', ucfirst($t->value)) => $t->value])
            ->toArray();
    }
}
