<?php

namespace App\Nova;

use App\Enums\BillingType;
use App\Enums\Gender;
use App\Enums\PatientType;
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

    public static $search = ['id', 'patient_number', 'first_name', 'last_name', 'id_number', 'email', 'phone'];

    public function subtitle(): ?string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Patient Number')->sortable()->rules('required')->creationRules('unique:patients,patient_number')->updateRules('unique:patients,patient_number,{{resourceId}}'),
            Text::make('First Name')->sortable()->rules('required', 'max:255'),
            Text::make('Last Name')->sortable()->rules('required', 'max:255'),
            Text::make('Full Name', fn () => $this->full_name)->onlyOnIndex(),
            Text::make('ID Number')->nullable()->creationRules('nullable', 'unique:patients,id_number')->updateRules('nullable', 'unique:patients,id_number,{{resourceId}}'),
            Date::make('Date of Birth')->rules('required'),
            Select::make('Gender')->options(collect(Gender::cases())->mapWithKeys(fn ($g) => [$g->value => ucfirst($g->value)]))->rules('required')->displayUsingLabels(),
            Text::make('Phone')->rules('required', 'max:20'),
            Text::make('Email')->nullable()->rules('nullable', 'email'),
            Textarea::make('Address')->nullable()->hideFromIndex(),
            Select::make('Patient Type')->options(collect(PatientType::cases())->mapWithKeys(fn ($t) => [$t->value => str_replace('_', ' ', ucfirst($t->value))]))->rules('required')->displayUsingLabels(),
            Select::make('Billing Type')->options(collect(BillingType::cases())->mapWithKeys(fn ($t) => [$t->value => str_replace('_', ' ', ucfirst($t->value))]))->rules('required')->displayUsingLabels(),
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
}
