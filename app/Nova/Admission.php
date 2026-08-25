<?php

namespace App\Nova;

use App\Enums\AdmissionStatus;
use App\Nova\Actions\AdministerMedication;
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

    public function title(): string
    {
        $this->loadMissing('patient');
        $patientNumber = $this->patient?->patient_number ?? '';
        $patientName = $this->patient ? "{$this->patient->first_name} {$this->patient->last_name}" : 'Unknown';
        $date = $this->admitted_at ? $this->admitted_at->format('d M Y') : '';

        return "{$patientNumber} — {$patientName} — {$date}";
    }
    public static $tableStyle = 'tight';

    public static $search = ['id', 'reason_for_admission'];

    public static function searchableColumns(): array
    {
        return ['id', 'reason_for_admission', 'patient.patient_number', 'patient.first_name', 'patient.last_name'];
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
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
            Select::make('Status')->options(collect(AdmissionStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))->default('admitted')->rules('required')->searchable()->displayUsingLabels(),
            HasMany::make('Prescriptions'),
            HasMany::make('Medication Administrations', 'medicationAdministrations', MedicationAdministration::class),
            HasOne::make('Invoice'),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new AdministerMedication,
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
