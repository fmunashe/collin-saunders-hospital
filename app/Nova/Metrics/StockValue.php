<?php

namespace App\Nova\Metrics;

use App\Models\Medication;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class StockValue extends Value
{
    public $name = 'Inventory Value';

    public function calculate(NovaRequest $request): ValueResult
    {
        $value = (float) Medication::where('is_active', true)
            ->select(DB::raw('SUM(stock_quantity * unit_price) as value'))
            ->value('value');

        return $this->result(round($value, 2))->currency('$');
    }

    public function ranges(): array
    {
        return [];
    }
}
