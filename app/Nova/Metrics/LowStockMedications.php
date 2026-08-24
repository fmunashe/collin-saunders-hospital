<?php

namespace App\Nova\Metrics;

use App\Models\Medication;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class LowStockMedications extends Value
{
    public $name = 'Low Stock Medications';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->result(
            Medication::whereColumn('stock_quantity', '<=', 'reorder_level')
                ->where('is_active', true)
                ->count()
        );
    }

    public function ranges(): array
    {
        return [];
    }
}
