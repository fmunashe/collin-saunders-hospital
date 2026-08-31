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
    public static $tableStyle = 'tight';

    public static $search = ['id', 'description', 'tariff_code'];

    public static $displayInNavigation = false;

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Invoice')->searchable(),
            Text::make('Description')->rules('required', 'max:255'),
            Text::make('Tariff Code')->nullable(),
            Number::make('Quantity')->rules('required', 'integer', 'min:1')->default(1),
            Currency::make('Unit Price')->rules('required', 'numeric', 'min:0'),
            Currency::make('Total')->exceptOnForms()->help('Calculated automatically (quantity × unit price).'),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
