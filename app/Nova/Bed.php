<?php

namespace App\Nova;

use App\Enums\BedStatus;
use App\Nova\Metrics\BedOccupancy;
use App\Nova\Metrics\BedsByDepartment;
use App\Nova\Metrics\BedsByWardType;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Bed extends Resource
{
    public static $model = \App\Models\Bed::class;

    public static $title = 'bed_number';
    public static $tableStyle = 'tight';

    public static $search = ['id', 'bed_number'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Ward')->rules('required'),
            Text::make('Bed Number')->sortable()->rules('required', 'max:50'),
            Select::make('Status')->options(collect(BedStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))->default('available')->rules('required')->displayUsingLabels(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            new BedOccupancy,
            new BedsByWardType,
            new BedsByDepartment,
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
