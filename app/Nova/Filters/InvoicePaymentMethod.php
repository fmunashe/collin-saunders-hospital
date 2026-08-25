<?php

namespace App\Nova\Filters;

use App\Enums\BillingType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class InvoicePaymentMethod extends Filter
{
    public $name = 'Payment Method';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('payment_method', $value);
    }

    public function options(NovaRequest $request): array
    {
        return collect(BillingType::cases())
            ->mapWithKeys(fn ($t) => [str_replace('_', ' ', ucfirst($t->value)) => $t->value])
            ->toArray();
    }
}
