<?php

namespace App\Nova\Filters;

use App\Models\Invoice;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class InvoiceNumber extends Filter
{
    public $name = 'Invoice Number';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('invoice_number', $value);
    }

    public function options(NovaRequest $request): array
    {
        return Invoice::orderBy('invoice_number')
            ->pluck('invoice_number', 'invoice_number')
            ->mapWithKeys(fn ($number) => [$number => $number])
            ->toArray();
    }
}
