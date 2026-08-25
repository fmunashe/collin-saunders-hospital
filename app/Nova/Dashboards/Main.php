<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AdmissionsPerDay;
use App\Nova\Metrics\BedOccupancy;
use App\Nova\Metrics\CurrentAdmissions;
use App\Nova\Metrics\InvoicesByStatus;
use App\Nova\Metrics\LowStockMedications;
use App\Nova\Metrics\NewPatientsPerDay;
use App\Nova\Metrics\NoAccessMessage;
use App\Nova\Metrics\PatientsByBilling;
use App\Nova\Metrics\PatientsByType;
use App\Nova\Metrics\PrescriptionsByStatus;
use App\Nova\Metrics\RevenuePerDay;
use App\Nova\Metrics\StaffByDesignation;
use App\Nova\Metrics\TotalPatients;
use App\Nova\Metrics\TotalStaff;
use App\Nova\Metrics\TotalVisitsToday;
use App\Nova\Metrics\VisitsByStatus;
use App\Nova\Metrics\VisitsPerDay;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    public function cards(): array
    {
        $user = auth()->user();

        if ($user && $user->roles->isEmpty()) {
            return [
                (new NoAccessMessage)->width('full'),
            ];
        }

        return [
            // Row 1: Key value metrics
            (new TotalPatients)->width('1/4'),
            (new TotalVisitsToday)->width('1/4'),
            (new CurrentAdmissions)->width('1/4'),
            (new TotalStaff)->width('1/4'),

            // Row 2: Trends
            (new NewPatientsPerDay)->width('1/2'),
            (new VisitsPerDay)->width('1/2'),

            // Row 3: Partitions - Patients
            (new PatientsByType)->width('1/3'),
            (new PatientsByBilling)->width('1/3'),
            (new VisitsByStatus)->width('1/3'),

            // Row 4: Admissions & Beds
            (new AdmissionsPerDay)->width('1/2'),
            (new BedOccupancy)->width('1/2'),

            // Row 5: Pharmacy & Prescriptions
            (new LowStockMedications)->width('1/4'),
            (new PrescriptionsByStatus)->width('3/4'),

            // Row 6: Revenue & Billing
            (new RevenuePerDay)->width('1/2'),
            (new InvoicesByStatus)->width('1/2'),

            // Row 7: Staff
            (new StaffByDesignation)->width('full'),
        ];
    }
}
