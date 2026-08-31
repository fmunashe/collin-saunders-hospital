<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\Invoice;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

class ReportDataService
{
    /**
     * Map of report keys to their required permission and builder method.
     */
    public static function reports(): array
    {
        return [
            'patient-reports' => ['permission' => 'view-patient-reports', 'title' => 'Patient Report', 'method' => 'patient'],
            'outpatient-reports' => ['permission' => 'view-outpatient-reports', 'title' => 'Outpatient Report', 'method' => 'outpatient'],
            'inpatient-reports' => ['permission' => 'view-inpatient-reports', 'title' => 'Inpatient Report', 'method' => 'inpatient'],
            'pharmacy-reports' => ['permission' => 'view-pharmacy-reports', 'title' => 'Pharmacy Report', 'method' => 'pharmacy'],
            'financial-reports' => ['permission' => 'view-financial-reports', 'title' => 'Financial Report', 'method' => 'financial'],
            'referral-reports' => ['permission' => 'view-referral-reports', 'title' => 'Referral Report', 'method' => 'referral'],
            'staff-reports' => ['permission' => 'view-staff-reports', 'title' => 'Staff Report', 'method' => 'staff'],
        ];
    }

    /**
     * Build report sections for the given report key.
     * Each section = ['heading' => string, 'rows' => [[label, value], ...]].
     */
    public function build(string $key): array
    {
        $method = self::reports()[$key]['method'] ?? null;

        return $method && method_exists($this, $method) ? $this->{$method}() : [];
    }

    private function patient(): array
    {
        return [
            [
                'heading' => 'Summary',
                'rows' => [
                    ['Total Patients', Patient::count()],
                    ['Staff Patients', Patient::where('patient_type', 'staff')->count()],
                    ['Non-Staff Patients', Patient::where('patient_type', 'non_staff')->count()],
                    ['Cash Patients', Patient::where('billing_type', 'cash')->count()],
                    ['Medical Aid Patients', Patient::where('billing_type', 'medical_aid')->count()],
                    ['Currently Admitted (Inpatients)', Admission::where('status', 'admitted')->distinct('patient_id')->count('patient_id')],
                ],
            ],
            [
                'heading' => 'By Gender',
                'rows' => Patient::select('gender', DB::raw('count(*) as total'))
                    ->groupBy('gender')->get()
                    ->map(fn ($r) => [ucfirst((string) $r->getRawOriginal('gender')), $r->total])->toArray(),
            ],
        ];
    }

    private function outpatient(): array
    {
        return [
            [
                'heading' => 'Summary',
                'rows' => [
                    ['Total Visits', Visit::count()],
                    ['Visits Today', Visit::whereDate('visit_date', today())->count()],
                    ['Waiting', Visit::where('status', 'waiting')->count()],
                    ['In Progress', Visit::where('status', 'in_progress')->count()],
                    ['Completed', Visit::where('status', 'completed')->count()],
                    ['Cancelled', Visit::where('status', 'cancelled')->count()],
                ],
            ],
            [
                'heading' => 'Visits by Department',
                'rows' => Visit::join('departments', 'visits.department_id', '=', 'departments.id')
                    ->select('departments.name', DB::raw('count(*) as total'))
                    ->groupBy('departments.name')->get()
                    ->map(fn ($r) => [$r->name, $r->total])->toArray(),
            ],
            [
                'heading' => 'Visits by Doctor',
                'rows' => Visit::join('doctors', 'visits.doctor_id', '=', 'doctors.id')
                    ->select('doctors.name', DB::raw('count(*) as total'))
                    ->groupBy('doctors.name')->get()
                    ->map(fn ($r) => [$r->name, $r->total])->toArray(),
            ],
        ];
    }

    private function inpatient(): array
    {
        $discharged = Admission::whereNotNull('discharged_at')->get(['admitted_at', 'discharged_at']);
        $avgStay = $discharged->isEmpty() ? 0 : round($discharged->avg(fn ($a) => $a->admitted_at->diffInDays($a->discharged_at) ?: 1), 1);

        return [
            [
                'heading' => 'Summary',
                'rows' => [
                    ['Current Inpatients', Admission::where('status', 'admitted')->count()],
                    ['Total Admissions', Admission::count()],
                    ['Discharged', Admission::where('status', 'discharged')->count()],
                    ['Transferred', Admission::where('status', 'transferred')->count()],
                    ['Average Length of Stay (days)', $avgStay],
                ],
            ],
            [
                'heading' => 'Admissions by Department',
                'rows' => Admission::join('departments', 'admissions.department_id', '=', 'departments.id')
                    ->select('departments.name', DB::raw('count(*) as total'))
                    ->groupBy('departments.name')->get()
                    ->map(fn ($r) => [$r->name, $r->total])->toArray(),
            ],
        ];
    }

    private function pharmacy(): array
    {
        $stockValue = (float) Medication::where('is_active', true)
            ->select(DB::raw('SUM(stock_quantity * unit_price) as v'))->value('v');

        return [
            [
                'heading' => 'Inventory Summary',
                'rows' => [
                    ['Total Active Medications', Medication::where('is_active', true)->count()],
                    ['Low Stock Items', Medication::whereColumn('stock_quantity', '<=', 'reorder_level')->where('is_active', true)->count()],
                    ['Out of Stock Items', Medication::where('stock_quantity', 0)->where('is_active', true)->count()],
                    ['Expired Items', Medication::whereNotNull('expiry_date')->where('expiry_date', '<', now())->count()],
                    ['Inventory Value', '$'.number_format($stockValue, 2)],
                ],
            ],
            [
                'heading' => 'Prescriptions',
                'rows' => [
                    ['Total Prescriptions', Prescription::count()],
                    ['Pending', Prescription::where('status', 'pending')->count()],
                    ['Partially Dispensed', Prescription::where('status', 'partially_dispensed')->count()],
                    ['Dispensed', Prescription::where('status', 'dispensed')->count()],
                ],
            ],
            [
                'heading' => 'Low Stock Detail',
                'columns' => ['Medication', 'Stock', 'Reorder Level'],
                'rows' => Medication::whereColumn('stock_quantity', '<=', 'reorder_level')
                    ->where('is_active', true)->orderBy('stock_quantity')->get()
                    ->map(fn ($m) => [$m->name, $m->stock_quantity, $m->reorder_level])->toArray(),
            ],
        ];
    }

    private function financial(): array
    {
        $invoiced = (float) Invoice::sum('total_amount');
        $collected = (float) Invoice::sum('paid_amount');

        return [
            [
                'heading' => 'Summary',
                'rows' => [
                    ['Total Invoiced', '$'.number_format($invoiced, 2)],
                    ['Total Collected', '$'.number_format($collected, 2)],
                    ['Outstanding Balance', '$'.number_format($invoiced - $collected, 2)],
                    ['Total Invoices', Invoice::count()],
                ],
            ],
            [
                'heading' => 'Invoices by Status',
                'rows' => Invoice::select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')->get()
                    ->map(fn ($r) => [ucfirst(str_replace('_', ' ', (string) $r->getRawOriginal('status'))), $r->total])->toArray(),
            ],
            [
                'heading' => 'Revenue by Payment Method',
                'rows' => Invoice::select('payment_method', DB::raw('SUM(paid_amount) as total'))
                    ->groupBy('payment_method')->get()
                    ->map(fn ($r) => [ucfirst(str_replace('_', ' ', (string) $r->getRawOriginal('payment_method'))), '$'.number_format((float) $r->total, 2)])->toArray(),
            ],
        ];
    }

    private function referral(): array
    {
        return [
            [
                'heading' => 'Summary',
                'rows' => [
                    ['Total Referrals', Referral::count()],
                    ['Pending', Referral::where('status', 'pending')->count()],
                    ['Accepted', Referral::where('status', 'accepted')->count()],
                    ['Completed', Referral::where('status', 'completed')->count()],
                    ['Cancelled', Referral::where('status', 'cancelled')->count()],
                ],
            ],
            [
                'heading' => 'By Priority',
                'rows' => Referral::select('priority', DB::raw('count(*) as total'))
                    ->groupBy('priority')->get()
                    ->map(fn ($r) => [ucfirst((string) $r->getRawOriginal('priority')), $r->total])->toArray(),
            ],
        ];
    }

    private function staff(): array
    {
        return [
            [
                'heading' => 'Summary',
                'rows' => [
                    ['Total Staff', Staff::count()],
                    ['Active Staff', Staff::where('is_active', true)->count()],
                ],
            ],
            [
                'heading' => 'By Designation',
                'rows' => Staff::select('designation', DB::raw('count(*) as total'))
                    ->groupBy('designation')->get()
                    ->map(fn ($r) => [ucfirst(str_replace('_', ' ', (string) $r->getRawOriginal('designation'))), $r->total])->toArray(),
            ],
        ];
    }
}
