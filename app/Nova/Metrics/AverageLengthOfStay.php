<?php

namespace App\Nova\Metrics;

use App\Models\Admission;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class AverageLengthOfStay extends Value
{
    public $name = 'Avg Length of Stay (days)';

    public function calculate(NovaRequest $request): ValueResult
    {
        $discharged = Admission::whereNotNull('discharged_at')->get(['admitted_at', 'discharged_at']);

        if ($discharged->isEmpty()) {
            return $this->result(0);
        }

        $avgDays = $discharged->avg(function ($admission) {
            return $admission->admitted_at->diffInDays($admission->discharged_at) ?: 1;
        });

        return $this->result(round($avgDays, 1));
    }

    public function ranges(): array
    {
        return [];
    }
}
