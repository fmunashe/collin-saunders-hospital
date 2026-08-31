<?php

namespace App\Nova;

use App\Enums\VisitStatus;
use App\Nova\Actions\GenerateVisitInvoice;
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

    public function title(): string
    {
        $this->loadMissing('patient');
        $patientNumber = $this->patient?->patient_number ?? '';
        $patientName = $this->patient ? "{$this->patient->first_name} {$this->patient->last_name}" : 'Unknown';
        $date = $this->visit_date ? $this->visit_date->format('d M Y') : '';

        return "{$patientNumber} — {$patientName} — {$date}";
    }

    public static function searchableColumns(): array
    {
        return ['id', 'patient.patient_number', 'patient.first_name', 'patient.last_name'];
    }

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
            Select::make('Status')->options(collect(VisitStatus::cases())->mapWithKeys(fn ($s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]))->default('waiting')->rules('required')->searchable()->displayUsingLabels(),
            HasMany::make('Prescriptions'),
            HasOne::make('Invoice'),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return array_merge(parent::actions($request), [
            (new GenerateVisitInvoice)->showInline()->canRun(fn ($request, $visit) => $visit->invoice === null),
        ]);
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
