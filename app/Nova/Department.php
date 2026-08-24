<?php

namespace App\Nova;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Department extends Resource
{
    public static $model = \App\Models\Department::class;

    public static $title = 'name';
    public static $tableStyle = 'tight';

    public static $search = ['id', 'name', 'code'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Code')->sortable()->rules('required', 'max:50')->creationRules('unique:departments,code')->updateRules('unique:departments,code,{{resourceId}}'),
            Textarea::make('Description')->nullable(),
            Boolean::make('Active', 'is_active')->default(true),
            HasMany::make('Doctors'),
            HasMany::make('Wards'),
            HasMany::make('Visits'),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
