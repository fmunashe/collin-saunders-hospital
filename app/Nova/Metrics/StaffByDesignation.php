<?php

namespace App\Nova\Metrics;

use App\Models\Staff;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class StaffByDesignation extends Partition
{
    public $name = 'Staff by Designation';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Staff::class, 'designation')
            ->label(fn ($value) => match ($value) {
                'nurse' => 'Nurse',
                'lab_technician' => 'Lab Technician',
                'radiographer' => 'Radiographer',
                'pharmacist' => 'Pharmacist',
                'receptionist' => 'Receptionist',
                'support_staff' => 'Support Staff',
                'administrator' => 'Administrator',
                'technician' => 'Technician',
                'cleaner' => 'Cleaner',
                'porter' => 'Porter',
                'security_guard' => 'Security Guard',
                default => ucfirst(str_replace('_', ' ', $value)),
            });
    }
}
