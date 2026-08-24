<?php

namespace App\Nova;

use App\Enums\BillingType;
use App\Enums\InvoiceStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Invoice extends Resource
{
    public static $model = \App\Models\Invoice::class;

    public static $title = 'invoice_number';

    public static $search = ['id', 'invoice_number'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Invoice Number')->sortable()->rules('required')->creationRules('unique:invoices,invoice_number')->updateRules('unique:invoices,invoice_number,{{resourceId}}'),
            BelongsTo::make('Patient')->searchable()->rules('required'),
            BelongsTo::make('Visit')->nullable(),
            BelongsTo::make('Admission')->nullable(),
            Currency::make('Total Amount')->sortable()->rules('required'),
            Currency::make('Paid Amount')->sortable()->default(0),
            Currency::make('Balance', fn () => $this->balance)->onlyOnDetail(),
            Select::make('Payment Method')->options(collect(BillingType::cases())->mapWithKeys(fn ($t) => [$t->value => str_replace('_', ' ', ucfirst($t->value))]))->rules('required')->displayUsingLabels(),
            Select::make('Status')->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]))->default('pending')->rules('required')->displayUsingLabels(),
            Textarea::make('Notes')->nullable()->hideFromIndex(),
            HasMany::make('Items', 'items', InvoiceItem::class),
        ];
    }
}
