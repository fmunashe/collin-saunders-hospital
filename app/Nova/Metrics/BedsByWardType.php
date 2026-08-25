<?php

namespace App\Nova\Metrics;

use App\Models\Bed;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class BedsByWardType extends Partition
{
    public $name = 'Beds by Ward Type';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Bed::query()->join('wards', 'beds.ward_id', '=', 'wards.id'), 'wards.type')
            ->label(fn ($value) => match ($value) {
                'general' => 'General',
                'icu' => 'ICU',
                'maternity' => 'Maternity',
                'paediatric' => 'Paediatric',
                default => ucfirst($value),
            });
    }
}
