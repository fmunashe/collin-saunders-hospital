<?php

namespace App\Nova\Metrics;

use App\Models\Invoice;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class TotalInvoiced extends Value
{
    public $name = 'Total Invoiced';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->sum($request, Invoice::class, 'total_amount')->currency('$');
    }

    public function ranges(): array
    {
        return [
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
            365 => 'Year',
            'TODAY' => 'Today',
            'MTD' => 'Month To Date',
            'YTD' => 'Year To Date',
        ];
    }
}
