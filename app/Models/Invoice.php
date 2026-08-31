<?php

namespace App\Models;

use App\Enums\BillingType;
use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'payment_method' => BillingType::class,
            'status' => InvoiceStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });

        // When the paid amount changes, refresh the status automatically
        static::saving(function (Invoice $invoice) {
            if ($invoice->isDirty('paid_amount')) {
                $invoice->status = $invoice->determineStatus(
                    (float) $invoice->total_amount,
                    (float) $invoice->paid_amount
                );
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-';
        $latest = static::withTrashed()
            ->where('invoice_number', 'like', $prefix.'%')
            ->orderBy('invoice_number', 'desc')
            ->value('invoice_number');

        $nextNumber = $latest ? (int) substr($latest, strlen($prefix)) + 1 : 1;

        return $prefix.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->paid_amount;
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::Paid;
    }

    /**
     * Recalculate the invoice total from its line items and refresh the payment status.
     */
    public function recalculateTotal(): void
    {
        $total = (float) $this->items()->sum('total');

        $this->forceFill([
            'total_amount' => $total,
            'status' => $this->determineStatus($total, (float) $this->paid_amount),
        ])->saveQuietly();
    }

    /**
     * Derive the invoice status from amounts, preserving medical-aid workflow states.
     */
    public function determineStatus(float $total, float $paid): InvoiceStatus
    {
        // Preserve manual medical-aid workflow states
        if (in_array($this->status, [InvoiceStatus::SubmittedToMedicalAid, InvoiceStatus::Rejected], true)) {
            return $this->status;
        }

        if ($paid <= 0) {
            return InvoiceStatus::Pending;
        }

        if ($paid >= $total && $total > 0) {
            return InvoiceStatus::Paid;
        }

        return InvoiceStatus::PartiallyPaid;
    }
}
