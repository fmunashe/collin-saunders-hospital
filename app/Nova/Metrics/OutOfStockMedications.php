<?php

namespace App\Nova\Metrics;

use App\Models\Medication;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class OutOfStockMedications extends Value
{
    public $name = 'Out of Stock';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->result(
            Medication::where('stock_quantity', 0)
                ->where('is_active', true)
                ->count()
        );
    }

    public function ranges(): array
    {
        return [];
    }
}
