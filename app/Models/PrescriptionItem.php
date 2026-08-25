<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'dispensed' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (PrescriptionItem $item) {
            // Deduct stock when item is marked as dispensed
            if ($item->isDirty('dispensed') && $item->dispensed && ! $item->getOriginal('dispensed')) {
                $medication = $item->medication;
                $stockBefore = $medication->stock_quantity;
                $medication->decrement('stock_quantity', $item->quantity);

                StockMovement::create([
                    'medication_id' => $medication->id,
                    'user_id' => auth()->id(),
                    'type' => 'dispensed',
                    'quantity' => -$item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - $item->quantity,
                    'reference' => 'Prescription #' . $item->prescription_id,
                    'notes' => 'Dispensed via prescription',
                ]);
            }

            // Restore stock if dispensed is reverted
            if ($item->isDirty('dispensed') && ! $item->dispensed && $item->getOriginal('dispensed')) {
                $medication = $item->medication;
                $stockBefore = $medication->stock_quantity;
                $medication->increment('stock_quantity', $item->quantity);

                StockMovement::create([
                    'medication_id' => $medication->id,
                    'user_id' => auth()->id(),
                    'type' => 'returned',
                    'quantity' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore + $item->quantity,
                    'reference' => 'Prescription #' . $item->prescription_id,
                    'notes' => 'Dispensing reversed',
                ]);
            }
        });
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
