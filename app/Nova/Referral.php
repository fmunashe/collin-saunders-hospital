<?php

namespace App\Nova;

use App\Enums\ReferralPriority;
use App\Enums\ReferralStatus;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Referral extends Resource
{
    public static $model = \App\Models\Referral::class;

    public static $title = 'id';

    public static $search = ['id', 'referred_to_hospital', 'reason'];

    public function title(): string
    {
        $this->loadMissing('patient');
        $patientName = $this->patient ? "{$this->patient->patient_number} — {$this->patient->first_name} {$this->patient->last_name}" : 'Unknown';

        return "{$patientName} → {$this->referred_to_hospital}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Patient')->searchable()->rules('required'),
            BelongsTo::make('Referring Doctor', 'referringDoctor', Doctor::class)->searchable()->rules('required'),
            BelongsTo::make('Visit')->nullable()->searchable(),
            BelongsTo::make('Admission')->nullable()->searchable(),
            Text::make('Referred To Hospital', 'referred_to_hospital')->rules('required', 'max:255')->sortable(),
            Text::make('Referred To Doctor', 'referred_to_doctor')->nullable()->hideFromIndex(),
            Text::make('Referred To Department', 'referred_to_department')->nullable()->hideFromIndex(),
            Select::make('Priority')->options(collect(ReferralPriority::cases())->mapWithKeys(fn ($p) => [$p->value => ucfirst($p->value)]))->rules('required')->searchable()->displayUsingLabels(),
            Badge::make('Priority')->map([
                'routine' => 'info',
                'urgent' => 'warning',
                'emergency' => 'danger',
            ])->onlyOnIndex(),
            Textarea::make('Reason')->rules('required'),
            Textarea::make('Clinical Summary')->nullable()->hideFromIndex(),
            Textarea::make('Diagnosis')->nullable()->hideFromIndex(),
            Date::make('Referral Date')->rules('required')->sortable()->default(now()),
            Select::make('Status')->options(collect(ReferralStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))->default('pending')->rules('required')->searchable()->displayUsingLabels(),
            Textarea::make('Notes')->nullable()->hideFromIndex(),
        ];
    }
}
