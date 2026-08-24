<?php

namespace App\Nova\Metrics;

use App\Models\Doctor;
use App\Models\Staff;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class TotalStaff extends Value
{
    public $name = 'Total Staff';

    public function calculate(NovaRequest $request): ValueResult
    {
        $doctors = Doctor::where('is_active', true)->count();
        $staff = Staff::where('is_active', true)->count();

        return $this->result($doctors + $staff);
    }

    public function ranges(): array
    {
        return [];
    }
}
