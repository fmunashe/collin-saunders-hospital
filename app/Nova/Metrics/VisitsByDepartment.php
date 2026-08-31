<?php

namespace App\Nova\Metrics;

use App\Models\Visit;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class VisitsByDepartment extends Partition
{
    public $name = 'Visits by Department';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count(
            $request,
            Visit::query()->join('departments', 'visits.department_id', '=', 'departments.id'),
            'departments.name'
        )->label(fn ($value) => $value ?: 'Unassigned');
    }
}
