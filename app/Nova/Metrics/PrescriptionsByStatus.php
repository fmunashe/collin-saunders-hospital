<?php

namespace App\Nova\Metrics;

use App\Models\Prescription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class PrescriptionsByStatus extends Partition
{
    public $name = 'Prescriptions by Status';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Prescription::class, 'status')
            ->label(fn ($value) => match ($value) {
                'pending' => 'Pending',
                'dispensed' => 'Dispensed',
                'partially_dispensed' => 'Partially Dispensed',
                'cancelled' => 'Cancelled',
                default => ucfirst($value),
            });
    }
}
