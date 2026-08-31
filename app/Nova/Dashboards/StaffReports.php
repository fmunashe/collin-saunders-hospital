<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\StaffByDesignation;
use App\Nova\Metrics\TotalStaff;
use Laravel\Nova\Dashboard;

class StaffReports extends Dashboard
{
    public function __construct()
    {
        $this->canSee(fn ($request) => $request->user()?->can('view-staff-reports') ?? false);
    }

    public function label(): string
    {
        return 'Staff Reports';
    }

    public function uriKey(): string
    {
        return 'staff-reports';
    }

    public function cards(): array
    {
        return [
            (new TotalStaff)->width('1/3'),
            (new StaffByDesignation)->width('2/3'),
        ];
    }
}
