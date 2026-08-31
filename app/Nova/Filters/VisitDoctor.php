<?php

namespace App\Nova\Filters;

use App\Models\Doctor;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class VisitDoctor extends Filter
{
    public $name = 'Doctor';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('doctor_id', $value);
    }

    public function options(NovaRequest $request): array
    {
        return Doctor::orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }
}
