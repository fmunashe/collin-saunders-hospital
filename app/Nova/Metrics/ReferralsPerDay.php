<?php

namespace App\Nova\Metrics;

use App\Models\Referral;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class ReferralsPerDay extends Trend
{
    public $name = 'Referrals Per Day';

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByDays($request, Referral::class, 'referral_date');
    }

    public function ranges(): array
    {
        return [
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
        ];
    }
}
