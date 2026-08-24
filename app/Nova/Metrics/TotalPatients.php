<?php

namespace App\Nova\Metrics;

use App\Models\Patient;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class TotalPatients extends Value
{
    public $name = 'Total Patients';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->count($request, Patient::class);
    }

    public function ranges(): array
    {
        return [
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
            365 => '1 Year',
            'ALL' => 'All Time',
        ];
    }
}
