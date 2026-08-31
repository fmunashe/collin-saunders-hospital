<?php

namespace App\Nova\Metrics;

use App\Models\Prescription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class PrescriptionsPerDay extends Trend
{
    public $name = 'Prescriptions Per Day';

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByDays($request, Prescription::class, 'prescribed_at');
    }

    public function ranges(): array
    {
        return [
            7 => '7 Days',
            14 => '14 Days',
            30 => '30 Days',
            60 => '60 Days',
        ];
    }
}
