<?php

namespace App\Nova\Metrics;

use App\Models\Invoice;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class InvoicesByPaymentMethod extends Partition
{
    public $name = 'Invoices by Payment Method';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Invoice::class, 'payment_method')
            ->label(fn ($value) => match ($value) {
                'cash' => 'Cash',
                'medical_aid' => 'Medical Aid',
                default => ucfirst(str_replace('_', ' ', $value)),
            });
    }
}
