<?php

namespace App\Nova\Metrics;

use App\Models\Ward;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class WardsByType extends Partition
{
    public $name = 'Wards by Type';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Ward::class, 'type')
            ->label(fn ($value) => match ($value) {
                'general' => 'General',
                'icu' => 'ICU',
                'maternity' => 'Maternity',
                'paediatric' => 'Paediatric',
                default => ucfirst($value),
            });
    }
}
