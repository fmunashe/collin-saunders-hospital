<?php

namespace App\Models;

use App\Enums\AdmissionNoteType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionNote extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => AdmissionNoteType::class,
            'noted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AdmissionNote $note) {
            if (empty($note->author_id) && auth()->check()) {
                $note->author_id = auth()->id();
            }
        });
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
