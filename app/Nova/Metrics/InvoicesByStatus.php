<?php

namespace App\Nova\Metrics;

use App\Models\Invoice;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class InvoicesByStatus extends Partition
{
    public $name = 'Invoices by Status';

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Invoice::class, 'status')
            ->label(fn ($value) => match ($value) {
                'pending' => 'Pending',
                'partially_paid' => 'Partially Paid',
                'paid' => 'Paid',
                'submitted_to_medical_aid' => 'Submitted to Medical Aid',
                'rejected' => 'Rejected',
                default => ucfirst(str_replace('_', ' ', $value)),
            });
    }
}
