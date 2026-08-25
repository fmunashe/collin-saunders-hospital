<?php

namespace App\Nova\Metrics;

use App\Models\Bed;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class BedsByDepartment extends Partition
{
    public $name = 'Beds by Department';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count(
            $request,
            Bed::query()
                ->join('wards', 'beds.ward_id', '=', 'wards.id')
                ->join('departments', 'wards.department_id', '=', 'departments.id'),
            'departments.name'
        )->label(fn ($value) => $value ?: 'Unassigned');
    }
}
