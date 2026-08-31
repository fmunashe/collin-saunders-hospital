<?php

namespace App\Nova\Metrics;

use App\Models\Visit;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class VisitsByDoctor extends Partition
{
    public $name = 'Visits by Doctor';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count(
            $request,
            Visit::query()->join('doctors', 'visits.doctor_id', '=', 'doctors.id'),
            'doctors.name'
        )->label(fn ($value) => $value ?: 'Unassigned');
    }
}
