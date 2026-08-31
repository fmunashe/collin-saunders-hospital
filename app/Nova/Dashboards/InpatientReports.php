<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AdmissionsByDepartment;
use App\Nova\Metrics\AdmissionsByStatus;
use App\Nova\Metrics\AdmissionsPerDay;
use App\Nova\Metrics\AverageLengthOfStay;
use App\Nova\Metrics\BedOccupancy;
use App\Nova\Metrics\BedsByWardType;
use App\Nova\Metrics\CurrentInpatients;
use App\Nova\Metrics\DischargesPerDay;
use Laravel\Nova\Dashboard;

class InpatientReports extends Dashboard
{
    public function __construct()
    {
        $this->canSee(fn ($request) => $request->user()?->can('view-inpatient-reports') ?? false);
    }

    public function label(): string
    {
        return 'Inpatient Reports';
    }

    public function uriKey(): string
    {
        return 'inpatient-reports';
    }

    public function cards(): array
    {
        return [
            (new CurrentInpatients)->width('1/3'),
            (new AverageLengthOfStay)->width('1/3'),
            (new BedOccupancy)->width('1/3'),
            (new AdmissionsByStatus)->width('1/2'),
            (new AdmissionsByDepartment)->width('1/2'),
            (new BedsByWardType)->width('full'),
            (new AdmissionsPerDay)->width('1/2'),
            (new DischargesPerDay)->width('1/2'),
        ];
    }
}
