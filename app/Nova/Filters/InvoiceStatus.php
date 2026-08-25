<?php

namespace App\Nova\Filters;

use App\Enums\InvoiceStatus as InvoiceStatusEnum;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class InvoiceStatus extends Filter
{
    public $name = 'Status';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('status', $value);
    }

    public function options(NovaRequest $request): array
    {
        return collect(InvoiceStatusEnum::cases())
            ->mapWithKeys(fn ($s) => [str_replace('_', ' ', ucfirst($s->value)) => $s->value])
            ->toArray();
    }
}
