<?php

namespace App\Nova\Filters;

use App\Models\Patient;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class InvoicePatientNumber extends Filter
{
    public $name = 'Patient Number';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->whereHas('patient', function (Builder $q) use ($value) {
            $q->where('patient_number', $value);
        });
    }

    public function options(NovaRequest $request): array
    {
        return Patient::whereHas('invoices')
            ->orderBy('patient_number')
            ->pluck('patient_number', 'patient_number')
            ->mapWithKeys(fn ($number) => [$number => $number])
            ->toArray();
    }
}
