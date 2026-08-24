<?php

namespace App\Nova\Metrics;

use App\Models\Patient;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class PatientsByBilling extends Partition
{
    public $name = 'Patients by Billing';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Patient::class, 'billing_type')
            ->label(fn ($value) => match ($value) {
                'cash' => 'Cash',
                'medical_aid' => 'Medical Aid',
                default => ucfirst($value),
            });
    }
}
