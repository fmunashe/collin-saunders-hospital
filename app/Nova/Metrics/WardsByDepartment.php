<?php

namespace App\Nova\Metrics;

use App\Models\Ward;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class WardsByDepartment extends Partition
{
    public $name = 'Wards by Department';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Ward::query()->join('departments', 'wards.department_id', '=', 'departments.id'), 'departments.name')
            ->label(fn ($value) => $value ?: 'Unassigned');
    }
}
