<?php

namespace App\Nova\Metrics;

use App\Models\Referral;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class ReferralsByStatus extends Partition
{
    public $name = 'Referrals by Status';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Referral::class, 'status')
            ->label(fn ($value) => ucfirst((string) $value));
    }
}
