<?php

namespace App\Nova\Actions;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Http\Requests\NovaRequest;

class GenerateVisitInvoice extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Generate Invoice';

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        if ($models->count() > 1) {
            return Action::danger('Please generate invoices one visit at a time.');
        }

        /** @var Visit $visit */
        $visit = $models->first();
        $visit->loadMissing(['invoice', 'patient', 'prescriptions.items.medication']);

        if ($visit->invoice) {
            return Action::danger('This visit already has an invoice ('.$visit->invoice->invoice_number.').');
        }

        $consultationFee = (float) $fields->get('consultation_fee');
        $includeMedications = (bool) $fields->get('include_medications');

        $invoice = DB::transaction(function () use ($visit, $consultationFee, $includeMedications) {
            $invoice = Invoice::create([
                'patient_id' => $visit->patient_id,
                'visit_id' => $visit->id,
                'payment_method' => $visit->patient->billing_type->value,
                'paid_amount' => 0,
                'status' => 'pending',
            ]);

            if ($consultationFee > 0) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Consultation fee',
                    'tariff_code' => '0190',
                    'quantity' => 1,
                    'unit_price' => $consultationFee,
                ]);
            }

            if ($includeMedications) {
                foreach ($visit->prescriptions as $prescription) {
                    foreach ($prescription->items as $item) {
                        if (! $item->medication) {
                            continue;
                        }

                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'description' => $item->medication->name.' ('.$item->dosage.')',
                            'tariff_code' => 'MED',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->medication->unit_price,
                        ]);
                    }
                }
            }

            return $invoice;
        });

        $invoice->refresh();

        return Action::message("Invoice {$invoice->invoice_number} created (Total: {$invoice->total_amount}).");
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Currency::make('Consultation Fee', 'consultation_fee')
                ->default(config('hms.billing.consultation_fee'))
                ->rules('required', 'numeric', 'min:0'),
            Boolean::make('Include Dispensed Medications', 'include_medications')
                ->default(true)
                ->help('Adds all prescribed medication line items to the invoice.'),
        ];
    }
}
