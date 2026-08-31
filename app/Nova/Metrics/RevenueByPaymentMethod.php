<?php

namespace App\Nova\Metrics;

use App\Models\Invoice;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class RevenueByPaymentMethod extends Partition
{
    public $name = 'Revenue by Payment Method';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->sum($request, Invoice::class, 'paid_amount', 'payment_method')
            ->label(fn ($value) => match ($value) {
                'cash' => 'Cash',
                'medical_aid' => 'Medical Aid',
                default => ucfirst(str_replace('_', ' ', (string) $value)),
            });
    }
}
