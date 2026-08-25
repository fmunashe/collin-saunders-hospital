<?php

namespace App\Nova;

use App\Enums\VisitStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Visit extends Resource
{
    public static $model = \App\Models\Visit::class;

    public static $title = 'id';

    public static $tableStyle = 'tight';

    public static $search = ['id'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Patient')->searchable()->rules('required'),
            BelongsTo::make('Doctor')->searchable()->rules('required'),
            BelongsTo::make('Department')->rules('required'),
            DateTime::make('Visit Date')->rules('required')->sortable(),
            Textarea::make('Complaint')->nullable(),
            Textarea::make('Diagnosis')->nullable()->hideFromIndex(),
            Textarea::make('Notes')->nullable()->hideFromIndex(),
            Select::make('Status')->options(collect(VisitStatus::cases())->mapWithKeys(fn ($s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]))->default('waiting')->rules('required')->displayUsingLabels(),
            HasMany::make('Prescriptions'),
            HasOne::make('Invoice'),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
