<?php

namespace App\Nova\Actions;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem as InvoiceItemClass;
use LaravelDaily\Invoices\Invoice as DailyInvoice;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class DownloadInvoice extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Download Invoice';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        /** @var Invoice $invoice */
        $invoice = $models->first();
        $invoice->load(['patient', 'items']);

        $customer = new Buyer([
            'name' => $invoice->patient->first_name . ' ' . $invoice->patient->last_name,
            'phone' => $invoice->patient->phone,
            'custom_fields' => [
                'Patient Number' => $invoice->patient->patient_number,
                'Billing Type' => ucfirst(str_replace('_', ' ', $invoice->payment_method->value)),
            ],
        ]);

        $items = $invoice->items->map(function ($item) {
            return InvoiceItemClass::make($item->description)
                ->pricePerUnit($item->unit_price)
                ->quantity($item->quantity);
        })->toArray();

        $pdf = DailyInvoice::make()
            ->series('INV')
            ->sequence((int) str_replace('INV-', '', $invoice->invoice_number))
            ->serialNumberFormat('{SERIES}-{SEQUENCE}')
            ->buyer($customer)
            ->status($invoice->status->value === 'paid' ? 'PAID' : strtoupper(str_replace('_', ' ', $invoice->status->value)))
            ->addItems($items)
            ->filename($invoice->invoice_number)
            ->logo(public_path('images/logo.png'))
            ->save('public');

        $url = asset('storage/' . $invoice->invoice_number . '.pdf');

        return Action::download($url, $invoice->invoice_number . '.pdf');
    }

    public function fields(NovaRequest $request): array
    {
        return [];
    }
}
