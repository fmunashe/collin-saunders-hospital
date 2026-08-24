<?php

namespace App\Nova\Metrics;

use App\Models\Patient;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class PatientsByType extends Partition
{
    public $name = 'Patients by Type';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Patient::class, 'patient_type')
            ->label(fn ($value) => match ($value) {
                'staff' => 'Staff',
                'non_staff' => 'Non-Staff',
                default => ucfirst($value),
            });
    }
}
