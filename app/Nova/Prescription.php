<?php

namespace App\Nova;

use App\Enums\PrescriptionStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Prescription extends Resource
{
    public static $model = \App\Models\Prescription::class;

    public static $title = 'id';

    public static $tableStyle = 'tight';

    public static $search = ['id','patient.patient_number', 'patient.first_name', 'patient.last_name', 'doctor.name'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Patient')->searchable()->rules('required'),
            BelongsTo::make('Doctor')->searchable()->rules('required'),
            BelongsTo::make('Visit')->nullable(),
            BelongsTo::make('Admission')->nullable(),
            DateTime::make('Prescribed At')->rules('required')->sortable()->default(now()),
            DateTime::make('Dispensed At')->readonly()->sortable()->hideWhenCreating()->hideWhenUpdating(),
            BelongsTo::make('Dispensed By', 'dispensedBy', User::class)->readonly()->hideWhenCreating()->hideWhenUpdating()->nullable(),
            Select::make('Status')->options(collect(PrescriptionStatus::cases())->mapWithKeys(fn ($s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]))->default('pending')->rules('required')->searchable()->displayUsingLabels(),
            Textarea::make('Notes')->nullable()->hideFromIndex(),
            HasMany::make('Items', 'items', PrescriptionItem::class),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
