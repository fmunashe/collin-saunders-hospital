<?php

namespace App\Nova;

use App\Enums\BillingType;
use App\Enums\Gender;
use App\Enums\PatientType;
use App\Nova\Metrics\PatientsByBilling;
use App\Nova\Metrics\PatientsByGender;
use App\Nova\Metrics\PatientsByType;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Patient extends Resource
{
    public static $model = \App\Models\Patient::class;

    public static $title = 'patient_number';

    public static $tableStyle = 'tight';

    public static $search = ['id', 'patient_number', 'first_name', 'last_name', 'id_number', 'email', 'phone'];

    public function subtitle(): ?string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Patient Number')->sortable()->readonly()->hideWhenCreating(),
            Badge::make('Care Status', function () {
                return $this->isInpatient() ? 'Inpatient' : 'Outpatient';
            })->map([
                'Inpatient' => 'danger',
                'Outpatient' => 'success',
            ])->exceptOnForms(),
            Text::make('First Name')->sortable()->rules('required', 'max:255'),
            Text::make('Last Name')->sortable()->rules('required', 'max:255'),
            Text::make('Full Name', fn () => $this->full_name)->onlyOnIndex(),
            Text::make('ID Number')->nullable()->creationRules('nullable', 'unique:patients,id_number')->updateRules('nullable', 'unique:patients,id_number,{{resourceId}}'),
            Date::make('Date of Birth')->rules('required'),
            Select::make('Gender')->options(collect(Gender::cases())->mapWithKeys(fn ($g) => [$g->value => ucfirst($g->value)]))->rules('required')->searchable()->displayUsingLabels(),
            Text::make('Phone')->rules('required', 'max:20'),
            Text::make('Email')->nullable()->rules('nullable', 'email'),
            Textarea::make('Address')->nullable()->hideFromIndex(),
            Select::make('Patient Type')->options(collect(PatientType::cases())->mapWithKeys(fn ($t) => [$t->value => str_replace('_', ' ', ucfirst($t->value))]))->rules('required')->searchable()->displayUsingLabels(),
            Select::make('Billing Type')->options(collect(BillingType::cases())->mapWithKeys(fn ($t) => [$t->value => str_replace('_', ' ', ucfirst($t->value))]))->rules('required')->searchable()->displayUsingLabels(),
            Text::make('Blood Group')->nullable()->hideFromIndex(),
            Textarea::make('Allergies')->nullable()->hideFromIndex(),
            Text::make('Emergency Contact Name')->nullable()->hideFromIndex(),
            Text::make('Emergency Contact Phone')->nullable()->hideFromIndex(),
            Text::make('Emergency Contact Relationship')->nullable()->hideFromIndex(),
            HasOne::make('Medical Aid Detail', 'medicalAidDetail', MedicalAidDetail::class),
            HasMany::make('Visits'),
            HasMany::make('Admissions'),
            HasMany::make('Prescriptions'),
            HasMany::make('Invoices'),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            new PatientsByType,
            new PatientsByBilling,
            new PatientsByGender,
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
