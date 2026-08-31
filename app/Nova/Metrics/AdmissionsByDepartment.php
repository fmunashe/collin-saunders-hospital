<?php

namespace App\Nova\Metrics;

use App\Models\Admission;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class AdmissionsByDepartment extends Partition
{
    public $name = 'Admissions by Department';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count(
            $request,
            Admission::query()->join('departments', 'admissions.department_id', '=', 'departments.id'),
            'departments.name'
        )->label(fn ($value) => $value ?: 'Unassigned');
    }
}
