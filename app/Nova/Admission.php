<?php

namespace App\Nova;

use App\Enums\AdmissionStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Admission extends Resource
{
    public static $model = \App\Models\Admission::class;

    public static $title = 'id';

    public static $search = ['id', 'reason_for_admission'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Patient')->searchable()->rules('required'),
            BelongsTo::make('Doctor')->searchable()->rules('required'),
            BelongsTo::make('Department')->rules('required'),
            BelongsTo::make('Ward')->rules('required'),
            BelongsTo::make('Bed')->nullable(),
            DateTime::make('Admitted At')->rules('required')->sortable(),
            DateTime::make('Discharged At')->nullable(),
            Textarea::make('Reason for Admission')->rules('required'),
            Textarea::make('Diagnosis')->nullable()->hideFromIndex(),
            Textarea::make('Discharge Notes')->nullable()->hideFromIndex(),
            Select::make('Status')->options(collect(AdmissionStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))->default('admitted')->rules('required')->displayUsingLabels(),
            HasMany::make('Prescriptions'),
            HasOne::make('Invoice'),
        ];
    }
}
