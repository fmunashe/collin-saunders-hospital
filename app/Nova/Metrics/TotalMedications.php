<?php

namespace App\Nova\Metrics;

use App\Models\Medication;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class TotalMedications extends Value
{
    public $name = 'Total Medications';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->result(
            Medication::where('is_active', true)->count()
        );
    }

    public function ranges(): array
    {
        return [];
    }
}
