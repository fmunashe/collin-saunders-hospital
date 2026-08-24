<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class PrescriptionItem extends Resource
{
    public static $model = \App\Models\PrescriptionItem::class;

    public static $title = 'id';

    public static $search = ['id', 'dosage'];

    public static $displayInNavigation = false;

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Prescription'),
            BelongsTo::make('Medication')->searchable()->rules('required'),
            Text::make('Dosage')->rules('required', 'max:255'),
            Number::make('Quantity')->rules('required', 'integer', 'min:1'),
            Number::make('Duration (Days)', 'duration_days')->nullable(),
            Textarea::make('Instructions')->nullable(),
            Boolean::make('Dispensed')->default(false),
        ];
    }
}
