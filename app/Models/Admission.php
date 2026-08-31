<?php

namespace App\Models;

use App\Enums\AdmissionStatus;
use App\Enums\BedStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class Admission extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'admitted_at' => 'datetime',
            'discharged_at' => 'datetime',
            'status' => AdmissionStatus::class,
        ];
    }

    protected static function booted(): void
    {
        // Prevent double-booking a bed before saving
        static::saving(function (Admission $admission) {
            if ($admission->bed_id && $admission->isActive()) {
                $conflict = static::query()
                    ->where('bed_id', $admission->bed_id)
                    ->where('status', AdmissionStatus::Admitted->value)
                    ->when($admission->exists, fn ($q) => $q->where('id', '!=', $admission->id))
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'bed_id' => 'This bed is already occupied by another admitted patient.',
                    ]);
                }
            }

            // Auto-stamp discharge time when leaving admitted status
            if ($admission->isDirty('status') && $admission->status !== AdmissionStatus::Admitted && ! $admission->discharged_at) {
                $admission->discharged_at = now();
            }
        });

        // Occupy the bed when a patient is admitted
        static::created(function (Admission $admission) {
            $admission->syncBedStatus();
        });

        // Manage bed status on admission changes (transfer, discharge, bed reassignment)
        static::updated(function (Admission $admission) {
            // Free the previous bed if it changed
            if ($admission->wasChanged('bed_id')) {
                $previousBedId = $admission->getOriginal('bed_id');
                if ($previousBedId) {
                    Bed::where('id', $previousBedId)->update(['status' => BedStatus::Available->value]);
                }
            }

            $admission->syncBedStatus();
        });
    }

    /**
     * Whether this admission currently occupies a bed.
     */
    public function isActive(): bool
    {
        return $this->status === AdmissionStatus::Admitted;
    }

    /**
     * Keep the linked bed's status in sync with the admission status.
     */
    public function syncBedStatus(): void
    {
        if (! $this->bed_id) {
            return;
        }

        $bed = $this->bed()->first();

        if (! $bed) {
            return;
        }

        if ($this->status === AdmissionStatus::Admitted) {
            $bed->update(['status' => BedStatus::Occupied->value]);
        } else {
            // Discharged, transferred, or deceased — free the bed
            $bed->update(['status' => BedStatus::Available->value]);
        }
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicationAdministrations(): HasMany
    {
        return $this->hasMany(MedicationAdministration::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
