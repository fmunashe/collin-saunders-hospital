<?php

namespace App\Nova\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class MedicationStockStatus extends Filter
{
    public $name = 'Stock Status';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return match ($value) {
            'out_of_stock' => $query->where('stock_quantity', 0),
            'low_stock' => $query->where('stock_quantity', '>', 0)->whereColumn('stock_quantity', '<=', 'reorder_level'),
            'in_stock' => $query->whereColumn('stock_quantity', '>', 'reorder_level'),
            default => $query,
        };
    }

    public function options(NovaRequest $request): array
    {
        return [
            'Out of Stock' => 'out_of_stock',
            'Low Stock' => 'low_stock',
            'In Stock' => 'in_stock',
        ];
    }
}
