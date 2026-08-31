<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // Always derive the line total from quantity × unit price
        static::saving(function (InvoiceItem $item) {
            $item->total = round((float) $item->unit_price * (int) $item->quantity, 2);
        });

        // Keep the parent invoice total in sync
        static::saved(fn (InvoiceItem $item) => $item->invoice?->recalculateTotal());
        static::deleted(fn (InvoiceItem $item) => $item->invoice?->recalculateTotal());
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
