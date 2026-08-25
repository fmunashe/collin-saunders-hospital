<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

class NoAccess extends Dashboard
{
    public function name(): string
    {
        return 'Dashboard';
    }

    public function cards(): array
    {
        return [
            (new \App\Nova\Metrics\NoAccessMessage)->width('full'),
        ];
    }
}
