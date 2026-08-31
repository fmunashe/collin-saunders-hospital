<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\TotalVisitsToday;
use App\Nova\Metrics\VisitsByDepartment;
use App\Nova\Metrics\VisitsByDoctor;
use App\Nova\Metrics\VisitsByStatus;
use App\Nova\Metrics\VisitsPerDay;
use Laravel\Nova\Dashboard;

class OutpatientReports extends Dashboard
{
    public function __construct()
    {
        $this->canSee(fn ($request) => $request->user()?->can('view-outpatient-reports') ?? false);
    }

    public function label(): string
    {
        return 'Outpatient Reports';
    }

    public function uriKey(): string
    {
        return 'outpatient-reports';
    }

    public function cards(): array
    {
        return [
            (new TotalVisitsToday)->width('1/3'),
            (new VisitsByStatus)->width('1/3'),
            (new VisitsByDepartment)->width('1/3'),
            (new VisitsByDoctor)->width('1/2'),
            (new VisitsPerDay)->width('1/2'),
        ];
    }
}
