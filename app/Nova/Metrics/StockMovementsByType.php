<?php

namespace App\Nova\Metrics;

use App\Models\StockMovement;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class StockMovementsByType extends Partition
{
    public $name = 'Stock Movements by Type';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, StockMovement::class, 'type')
            ->label(fn ($value) => ucfirst((string) $value));
    }
}
