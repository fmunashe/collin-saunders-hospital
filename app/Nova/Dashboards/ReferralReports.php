<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\ReferralsByPriority;
use App\Nova\Metrics\ReferralsByStatus;
use App\Nova\Metrics\ReferralsPerDay;
use Laravel\Nova\Dashboard;

class ReferralReports extends Dashboard
{
    public function __construct()
    {
        $this->canSee(fn ($request) => $request->user()?->can('view-referral-reports') ?? false);
    }

    public function label(): string
    {
        return 'Referral Reports';
    }

    public function uriKey(): string
    {
        return 'referral-reports';
    }

    public function cards(): array
    {
        return [
            (new ReferralsByStatus)->width('1/2'),
            (new ReferralsByPriority)->width('1/2'),
            (new ReferralsPerDay)->width('full'),
        ];
    }
}
