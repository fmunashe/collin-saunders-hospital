<?php

namespace App\Nova\Filters;

use App\Enums\BillingType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class PatientBillingType extends Filter
{
    public $name = 'Billing Type';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('billing_type', $value);
    }

    public function options(NovaRequest $request): array
    {
        return collect(BillingType::cases())
            ->mapWithKeys(fn ($t) => [str_replace('_', ' ', ucfirst($t->value)) => $t->value])
            ->toArray();
    }
}
