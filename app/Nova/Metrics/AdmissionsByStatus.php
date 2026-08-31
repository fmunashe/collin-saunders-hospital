<?php

namespace App\Nova\Metrics;

use App\Models\Admission;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class AdmissionsByStatus extends Partition
{
    public $name = 'Admissions by Status';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Admission::class, 'status')
            ->label(fn ($value) => match ($value) {
                'admitted' => 'Admitted',
                'discharged' => 'Discharged',
                'transferred' => 'Transferred',
                'deceased' => 'Deceased',
                default => ucfirst((string) $value),
            });
    }
}
