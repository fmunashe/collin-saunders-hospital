<?php

namespace App\Nova\Metrics;

use App\Models\Referral;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class ReferralsByPriority extends Partition
{
    public $name = 'Referrals by Priority';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Referral::class, 'priority')
            ->label(fn ($value) => ucfirst((string) $value))
            ->colors([
                'routine' => '#3b82f6',
                'urgent' => '#f59e0b',
                'emergency' => '#ef4444',
            ]);
    }
}
