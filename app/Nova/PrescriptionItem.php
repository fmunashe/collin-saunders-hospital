<?php

namespace App\Nova;

use App\Nova\Actions\BulkDispense;
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

    public static $tableStyle = 'tight';
    public static $title = 'id';

    public static $search = ['id', 'dosage'];

    public static $displayInNavigation = false;

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Prescription'),
            BelongsTo::make('Medication')->searchable()->rules('required'),
            Text::make('Dosage')->rules('required', 'max:255'),
            Number::make('Quantity')->rules('required', 'integer', 'min:1'),
            Number::make('Duration (Days)', 'duration_days')->nullable(),
            Textarea::make('Instructions')->nullable(),
            Boolean::make('Dispensed')->default(false),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new BulkDispense,
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
