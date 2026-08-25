<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class MedicalAidDetail extends Resource
{
    public static $model = \App\Models\MedicalAidDetail::class;

    public static $title = 'membership_number';

    public static $search = ['id', 'membership_number', 'main_member_name'];

    public static $displayInNavigation = false;

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Patient'),
            BelongsTo::make('Medical Aid Scheme', 'medicalAidScheme', MedicalAidScheme::class)->rules('required'),
            Text::make('Membership Number')->rules('required', 'max:255'),
            Text::make('Plan Name')->nullable(),
            Text::make('Main Member Name')->nullable(),
            Text::make('Dependency Code')->default('00'),
            Date::make('Valid From')->nullable(),
            Date::make('Valid Until')->nullable(),
        ];
    }
}
