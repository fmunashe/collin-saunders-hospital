<?php

namespace App\Nova;

use App\Enums\AdmissionStatus;
use App\Nova\Actions\AdministerMedication;
use App\Nova\Actions\DischargePatient;
use App\Nova\Filters\AdmissionDepartment;
use App\Nova\Filters\AdmissionDoctor;
use App\Nova\Filters\AdmissionStatusFilter;
use App\Nova\Filters\AdmissionWard;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Tabs\Tab;
use Laravel\Nova\Tabs\TabsGroup;

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
            BelongsTo::make('Department')->searchable()->rules('required'),
            BelongsTo::make('Ward')->searchable()->rules('required'),
            BelongsTo::make('Bed')->nullable()->searchable()->relatableQueryUsing(function (NovaRequest $request, $query) {
                // Show only available beds, plus the bed already assigned to this admission
                $query->where(function ($q) use ($request) {
                    $q->where('status', 'available');

                    if ($request->resourceId) {
                        $admission = \App\Models\Admission::find($request->resourceId);
                        if ($admission && $admission->bed_id) {
                            $q->orWhere('id', $admission->bed_id);
                        }
                    }
                });
            }),
            DateTime::make('Admitted At')->rules('required')->sortable(),
            DateTime::make('Discharged At')->nullable(),
            Textarea::make('Reason for Admission')->rules('required'),
            Textarea::make('Diagnosis')->nullable()->hideFromIndex(),
            Textarea::make('Discharge Notes')->nullable()->hideFromIndex(),
            Select::make('Status')->options(collect(AdmissionStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))->default('admitted')->rules('required')->searchable()->displayUsingLabels(),

            TabsGroup::make('Patient Medical Information', [
                Tab::make('Patient Care Notes',[
                    HasMany::make('Notes', 'notes', AdmissionNote::class),
                  ]),
                Tab::make('Prescriptions',[
                    HasMany::make('Prescriptions'),
                  ]),
                Tab::make('Medication Administrations',[
                    HasMany::make('Medication Administrations', 'medicationAdministrations', MedicationAdministration::class),
                  ]),
                Tab::make('Invoice',[
                    HasOne::make('Invoice'),
                ]),

            ]),

//            HasMany::make('Notes', 'notes', AdmissionNote::class),
//            HasMany::make('Prescriptions'),
//            HasMany::make('Medication Administrations', 'medicationAdministrations', MedicationAdministration::class),
//            HasOne::make('Invoice'),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new AdmissionDepartment,
            new AdmissionStatusFilter,
            new AdmissionWard,
            new AdmissionDoctor,
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return array_merge(parent::actions($request), [
            (new DischargePatient)
                ->confirmButtonText('Discharge')
                ->canRun(fn ($request, $admission) => $admission->status === AdmissionStatus::Admitted
                    && $request->user()?->can('admission-update')),
            (new AdministerMedication)
                ->canRun(fn ($request, $admission) => $admission->status === AdmissionStatus::Admitted
                    && $request->user()?->can('medication-administration-create')),
        ]);
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
