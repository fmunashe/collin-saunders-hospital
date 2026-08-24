<?php

namespace App\Nova;

use App\Enums\BedStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Bed extends Resource
{
    public static $model = \App\Models\Bed::class;

    public static $title = 'bed_number';

    public static $search = ['id', 'bed_number'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Ward')->rules('required'),
            Text::make('Bed Number')->sortable()->rules('required', 'max:50'),
            Select::make('Status')->options(collect(BedStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))->default('available')->rules('required')->displayUsingLabels(),
        ];
    }
}
