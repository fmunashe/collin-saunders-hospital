<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\DispensedPerDay;
use App\Nova\Metrics\ExpiringMedications;
use App\Nova\Metrics\LowStockMedications;
use App\Nova\Metrics\OutOfStockMedications;
use App\Nova\Metrics\PrescriptionsByStatus;
use App\Nova\Metrics\PrescriptionsPerDay;
use App\Nova\Metrics\StockMovementsByType;
use App\Nova\Metrics\StockValue;
use App\Nova\Metrics\TotalMedications;
use Laravel\Nova\Dashboard;

class PharmacyReports extends Dashboard
{
    public function __construct()
    {
        $this->canSee(fn ($request) => $request->user()?->can('view-pharmacy-reports') ?? false);
    }

    public function label(): string
    {
        return 'Pharmacy Reports';
    }

    public function uriKey(): string
    {
        return 'pharmacy-reports';
    }

    public function cards(): array
    {
        return [
            (new TotalMedications)->width('1/4'),
            (new LowStockMedications)->width('1/4'),
            (new OutOfStockMedications)->width('1/4'),
            (new ExpiringMedications)->width('1/4'),
            (new StockValue)->width('1/3'),
            (new StockMovementsByType)->width('1/3'),
            (new PrescriptionsByStatus)->width('1/3'),
            (new PrescriptionsPerDay)->width('1/2'),
            (new DispensedPerDay)->width('1/2'),
        ];
    }
}
