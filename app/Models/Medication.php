<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medication extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->reorder_level;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(): bool
    {
        if (! $this->expiry_date || $this->isExpired()) {
            return false;
        }

        $warningDays = (int) config('hms.pharmacy.expiry_warning_days', 90);

        return $this->expiry_date->isBefore(now()->addDays($warningDays));
    }

    /**
     * Human-readable stock status label.
     */
    public function stockStatusLabel(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'Out of Stock';
        }

        if ($this->isLowStock()) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    /**
     * Human-readable expiry status label.
     */
    public function expiryStatusLabel(): string
    {
        if (! $this->expiry_date) {
            return 'No Expiry';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        if ($this->isExpiringSoon()) {
            return 'Expiring Soon';
        }

        return 'Valid';
    }
}
