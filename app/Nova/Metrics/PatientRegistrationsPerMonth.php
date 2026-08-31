<?php

namespace App\Nova\Metrics;

use App\Models\Patient;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class PatientRegistrationsPerMonth extends Trend
{
    public $name = 'Patient Registrations Per Month';

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByMonths($request, Patient::class);
    }

    public function ranges(): array
    {
        return [
            6 => '6 Months',
            12 => '12 Months',
            24 => '24 Months',
        ];
    }
}
