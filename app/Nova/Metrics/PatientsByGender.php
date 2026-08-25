<?php

namespace App\Nova\Metrics;

use App\Models\Patient;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class PatientsByGender extends Partition
{
    public $name = 'Patients by Gender';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Patient::class, 'gender')
            ->label(fn ($value) => match ($value) {
                'male' => 'Male',
                'female' => 'Female',
                'other' => 'Other',
                default => ucfirst($value),
            });
    }
}
