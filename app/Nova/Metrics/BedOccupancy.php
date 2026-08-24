<?php

namespace App\Nova\Metrics;

use App\Models\Bed;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class BedOccupancy extends Partition
{
    public $name = 'Bed Occupancy';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Bed::class, 'status')
            ->label(fn ($value) => match ($value) {
                'available' => 'Available',
                'occupied' => 'Occupied',
                'maintenance' => 'Maintenance',
                default => ucfirst($value),
            });
    }
}
