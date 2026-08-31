<?php

namespace App\Nova\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class MedicationExpiryStatus extends Filter
{
    public $name = 'Expiry Status';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        $warningDate = now()->addDays((int) config('hms.pharmacy.expiry_warning_days', 90));

        return match ($value) {
            'expired' => $query->whereNotNull('expiry_date')->where('expiry_date', '<', now()),
            'expiring_soon' => $query->whereNotNull('expiry_date')
                ->where('expiry_date', '>=', now())
                ->where('expiry_date', '<=', $warningDate),
            'valid' => $query->whereNotNull('expiry_date')->where('expiry_date', '>', $warningDate),
            default => $query,
        };
    }

    public function options(NovaRequest $request): array
    {
        return [
            'Expired' => 'expired',
            'Expiring Soon' => 'expiring_soon',
            'Valid' => 'valid',
        ];
    }
}
