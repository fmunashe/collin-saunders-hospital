<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Doctor extends Resource
{
    public static $model = \App\Models\Doctor::class;

    public static $title = 'name';
    public static $tableStyle = 'tight';

    public static $search = ['id', 'name', 'practice_number', 'specialisation'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('User')->nullable()->searchable(),
            BelongsTo::make('Department')->rules('required'),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Practice Number')->sortable()->rules('required')->creationRules('unique:doctors,practice_number')->updateRules('unique:doctors,practice_number,{{resourceId}}'),
            Text::make('Specialisation')->nullable(),
            Text::make('Phone')->nullable(),
            Text::make('Email')->nullable()->rules('nullable', 'email'),
            Boolean::make('Active', 'is_active')->default(true),
            HasMany::make('Visits'),
            HasMany::make('Admissions'),
            HasMany::make('Prescriptions'),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
