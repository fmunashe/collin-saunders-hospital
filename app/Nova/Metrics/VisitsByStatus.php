<?php

namespace App\Nova\Metrics;

use App\Models\Visit;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class VisitsByStatus extends Partition
{
    public $name = 'Visits by Status';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Visit::class, 'status')
            ->label(fn ($value) => match ($value) {
                'waiting' => 'Waiting',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                default => ucfirst($value),
            });
    }
}
