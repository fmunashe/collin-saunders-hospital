<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\InvoicesByPaymentMethod;
use App\Nova\Metrics\InvoicesByStatus;
use App\Nova\Metrics\OutstandingBalance;
use App\Nova\Metrics\RevenueByPaymentMethod;
use App\Nova\Metrics\RevenuePerDay;
use App\Nova\Metrics\TotalInvoiced;
use App\Nova\Metrics\TotalRevenue;
use Laravel\Nova\Dashboard;

class FinancialReports extends Dashboard
{
    public function __construct()
    {
        $this->canSee(fn ($request) => $request->user()?->can('view-financial-reports') ?? false);
    }

    public function label(): string
    {
        return 'Financial Reports';
    }

    public function uriKey(): string
    {
        return 'financial-reports';
    }

    public function cards(): array
    {
        return [
            (new TotalInvoiced)->width('1/3'),
            (new TotalRevenue)->width('1/3'),
            (new OutstandingBalance)->width('1/3'),
            (new InvoicesByStatus)->width('1/3'),
            (new InvoicesByPaymentMethod)->width('1/3'),
            (new RevenueByPaymentMethod)->width('1/3'),
            (new RevenuePerDay)->width('full'),
        ];
    }
}
