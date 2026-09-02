<?php

namespace App\Nova;

use App\Enums\Gender;
use App\Enums\WardType;
use App\Nova\Metrics\WardsByDepartment;
use App\Nova\Metrics\WardsByType;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Ward extends Resource
{
    public static $model = \App\Models\Ward::class;

    public static $title = 'name';

    public static $tableStyle = 'tight';

    public static $search = ['id', 'name'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Department')->rules('required'),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Select::make('Type')->options(collect(WardType::cases())->mapWithKeys(fn ($t) => [$t->value => ucfirst($t->value)]))->rules('required')->searchable()->displayUsingLabels(),
            Select::make('Gender Restriction', 'gender_restriction')
                ->options([
                    Gender::Male->value => 'Male only',
                    Gender::Female->value => 'Female only',
                ])
                ->nullable()
                ->searchable()
                ->displayUsingLabels()
                ->help('Leave empty for a mixed/any-gender ward. Maternity wards are always female-only regardless of this setting.'),
            Number::make('Capacity')->rules('required', 'integer', 'min:1'),
            Boolean::make('Active', 'is_active')->default(true),
            HasMany::make('Beds'),
            HasMany::make('Admissions'),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            (new WardsByType)->width('1/2'),
            (new WardsByDepartment)->width('1/2'),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
