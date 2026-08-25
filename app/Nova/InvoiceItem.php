<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class InvoiceItem extends Resource
{
    public static $model = \App\Models\InvoiceItem::class;

    public static $title = 'description';

    public static $search = ['id', 'description', 'tariff_code'];

    public static $displayInNavigation = false;

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Invoice'),
            Text::make('Description')->rules('required', 'max:255'),
            Text::make('Tariff Code')->nullable(),
            Number::make('Quantity')->rules('required', 'integer', 'min:1')->default(1),
            Currency::make('Unit Price')->rules('required'),
            Currency::make('Total')->rules('required'),
        ];
    }
}
