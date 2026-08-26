<?php

namespace App\Models;

use App\Enums\BillingType;
use App\Enums\Gender;
use App\Enums\PatientType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            if (empty($patient->patient_number)) {
                $patient->patient_number = static::generatePatientNumber();
            }
        });
    }

    public static function generatePatientNumber(): string
    {
        $prefix = 'PT';
        $latest = static::withTrashed()
            ->where('patient_number', 'like', $prefix.'%')
            ->orderBy('patient_number', 'desc')
            ->value('patient_number');

        $nextNumber = $latest ? (int) substr($latest, strlen($prefix)) + 1 : 1;

        return $prefix.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'gender' => Gender::class,
            'patient_type' => PatientType::class,
            'billing_type' => BillingType::class,
        ];
    }

    public function medicalAidDetail(): HasOne
    {
        return $this->hasOne(MedicalAidDetail::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isStaff(): bool
    {
        return $this->patient_type === PatientType::Staff;
    }

    public function isMedicalAid(): bool
    {
        return $this->billing_type === BillingType::MedicalAid;
    }

    public function isInpatient(): bool
    {
        return $this->admissions()->where('status', 'admitted')->exists();
    }
}
