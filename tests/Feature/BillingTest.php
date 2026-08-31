<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    private function makeInvoice(): Invoice
    {
        $patient = Patient::create([
            'first_name' => 'Bill',
            'last_name' => 'Payer',
            'date_of_birth' => '1980-01-01',
            'gender' => 'female',
            'phone' => '+27000000000',
            'patient_type' => 'non_staff',
            'billing_type' => 'cash',
        ]);

        return Invoice::create([
            'patient_id' => $patient->id,
            'payment_method' => 'cash',
            'paid_amount' => 0,
        ]);
    }

    public function test_invoice_number_is_auto_generated(): void
    {
        $invoice = $this->makeInvoice();

        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
    }

    public function test_invoice_item_total_is_calculated_automatically(): void
    {
        $invoice = $this->makeInvoice();

        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consultation',
            'quantity' => 3,
            'unit_price' => 150.00,
        ]);

        $this->assertEquals(450.00, (float) $item->fresh()->total);
    }

    public function test_invoice_total_reflects_sum_of_items(): void
    {
        $invoice = $this->makeInvoice();

        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'A', 'quantity' => 2, 'unit_price' => 100]);
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'B', 'quantity' => 1, 'unit_price' => 250]);

        $this->assertEquals(450.00, (float) $invoice->fresh()->total_amount);
    }

    public function test_invoice_total_recalculates_when_item_deleted(): void
    {
        $invoice = $this->makeInvoice();
        $item = InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'A', 'quantity' => 2, 'unit_price' => 100]);
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'B', 'quantity' => 1, 'unit_price' => 250]);
        $this->assertEquals(450.00, (float) $invoice->fresh()->total_amount);

        $item->delete();

        $this->assertEquals(250.00, (float) $invoice->fresh()->total_amount);
    }

    public function test_status_becomes_paid_when_fully_paid(): void
    {
        $invoice = $this->makeInvoice();
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'A', 'quantity' => 1, 'unit_price' => 500]);

        $invoice->refresh()->update(['paid_amount' => 500]);

        $this->assertEquals('paid', $invoice->fresh()->status->value);
    }

    public function test_status_becomes_partially_paid(): void
    {
        $invoice = $this->makeInvoice();
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'A', 'quantity' => 1, 'unit_price' => 500]);

        $invoice->refresh()->update(['paid_amount' => 200]);

        $this->assertEquals('partially_paid', $invoice->fresh()->status->value);
    }
}
