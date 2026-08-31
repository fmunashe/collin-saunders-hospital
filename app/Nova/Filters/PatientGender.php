<?php

namespace App\Nova\Filters;

use App\Enums\Gender;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class PatientGender extends Filter
{
    public $name = 'Gender';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('gender', $value);
    }

    public function options(NovaRequest $request): array
    {
        return collect(Gender::cases())
            ->mapWithKeys(fn ($g) => [ucfirst($g->value) => $g->value])
            ->toArray();
    }
}
