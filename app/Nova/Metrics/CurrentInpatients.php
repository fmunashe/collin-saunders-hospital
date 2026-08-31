<?php

namespace App\Nova\Metrics;

use App\Models\Admission;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class CurrentInpatients extends Value
{
    public $name = 'Current Inpatients';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->result(
            Admission::where('status', 'admitted')->count()
        );
    }

    public function ranges(): array
    {
        return [];
    }
}
