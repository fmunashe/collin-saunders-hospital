<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\WardType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => WardType::class,
            'gender_restriction' => Gender::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * The gender this ward is restricted to, if any.
     *
     * Maternity wards are implicitly female-only. Otherwise the explicit
     * gender_restriction column applies (null = any gender allowed).
     */
    public function effectiveGenderRestriction(): ?Gender
    {
        if ($this->type === WardType::Maternity) {
            return Gender::Female;
        }

        return $this->gender_restriction;
    }

    /**
     * Whether a patient of the given gender may be admitted to this ward.
     * Patients with gender "Other" are only allowed into unrestricted wards.
     */
    public function acceptsGender(?Gender $gender): bool
    {
        $restriction = $this->effectiveGenderRestriction();

        if ($restriction === null) {
            return true; // no restriction
        }

        return $gender === $restriction;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function availableBeds(): HasMany
    {
        return $this->hasMany(Bed::class)->where('status', 'available');
    }
}
