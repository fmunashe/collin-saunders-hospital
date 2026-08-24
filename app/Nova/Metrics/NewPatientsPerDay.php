<?php

namespace App\Nova\Metrics;

use App\Models\Patient;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class NewPatientsPerDay extends Trend
{
    public $name = 'New Patients';

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByDays($request, Patient::class);
    }

    public function ranges(): array
    {
        return [
            7 => '7 Days',
            14 => '14 Days',
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
        ];
    }
}
