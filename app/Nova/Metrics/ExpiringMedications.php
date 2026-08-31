<?php

namespace App\Nova\Metrics;

use App\Models\Medication;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class ExpiringMedications extends Value
{
    public $name = 'Expiring / Expired';

    public function calculate(NovaRequest $request): ValueResult
    {
        $warningDays = (int) config('hms.pharmacy.expiry_warning_days', 90);

        return $this->result(
            Medication::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays($warningDays))
                ->count()
        );
    }

    public function ranges(): array
    {
        return [];
    }
}
