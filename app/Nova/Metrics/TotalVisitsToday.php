<?php

namespace App\Nova\Metrics;

use App\Models\Visit;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class TotalVisitsToday extends Value
{
    public $name = "Today's Visits";

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->count($request, Visit::class);
    }

    public function ranges(): array
    {
        return [
            'TODAY' => 'Today',
            30 => '30 Days',
            60 => '60 Days',
            365 => '1 Year',
            'ALL' => 'All Time',
        ];
    }
}
