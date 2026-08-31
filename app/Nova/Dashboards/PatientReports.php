<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\NewPatientsPerDay;
use App\Nova\Metrics\PatientRegistrationsPerMonth;
use App\Nova\Metrics\PatientsByBilling;
use App\Nova\Metrics\PatientsByGender;
use App\Nova\Metrics\PatientsByType;
use App\Nova\Metrics\TotalPatients;
use Laravel\Nova\Dashboard;

class PatientReports extends Dashboard
{
    public function __construct()
    {
        $this->canSee(fn ($request) => $request->user()?->can('view-patient-reports') ?? false);
    }

    public function label(): string
    {
        return 'Patient Reports';
    }

    public function uriKey(): string
    {
        return 'patient-reports';
    }

    public function cards(): array
    {
        return [
            (new TotalPatients)->width('1/3'),
            (new PatientsByType)->width('1/3'),
            (new PatientsByGender)->width('1/3'),
            (new PatientsByBilling)->width('1/2'),
            (new NewPatientsPerDay)->width('1/2'),
            (new PatientRegistrationsPerMonth)->width('full'),
        ];
    }
}
