<?php

namespace App\Nova\Filters;

use App\Models\Ward;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class AdmissionWard extends Filter
{
    public $name = 'Ward';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('ward_id', $value);
    }

    public function options(NovaRequest $request): array
    {
        return Ward::orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }
}
