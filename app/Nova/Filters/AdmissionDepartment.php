<?php

namespace App\Nova\Filters;

use App\Models\Department;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class AdmissionDepartment extends Filter
{
    public $name = 'Department';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('department_id', $value);
    }

    public function options(NovaRequest $request): array
    {
        return Department::orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }
}
