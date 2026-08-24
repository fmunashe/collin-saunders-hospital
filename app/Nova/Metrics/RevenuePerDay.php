<?php

namespace App\Nova\Metrics;

use App\Models\Invoice;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class RevenuePerDay extends Trend
{
    public $name = 'Revenue';

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->sumByDays($request, Invoice::class, 'paid_amount');
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
