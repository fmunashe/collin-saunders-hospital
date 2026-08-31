<?php

namespace App\Nova\Metrics;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class OutstandingBalance extends Value
{
    public $name = 'Outstanding Balance';

    public function calculate(NovaRequest $request): ValueResult
    {
        $outstanding = (float) Invoice::query()
            ->whereNotIn('status', ['paid'])
            ->select(DB::raw('SUM(total_amount - paid_amount) as balance'))
            ->value('balance');

        return $this->result(round($outstanding, 2))->currency('$');
    }

    public function ranges(): array
    {
        return [];
    }
}
