<?php

namespace App\Nova;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class MedicalAidScheme extends Resource
{
    public static $model = \App\Models\MedicalAidScheme::class;

    public static $title = 'name';

    public static $search = ['id', 'name', 'code'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Code')->sortable()->rules('required', 'max:50')->creationRules('unique:medical_aid_schemes,code')->updateRules('unique:medical_aid_schemes,code,{{resourceId}}'),
            Text::make('Contact Number')->nullable(),
            Text::make('Email')->nullable()->rules('nullable', 'email'),
            Textarea::make('Address')->nullable(),
            Boolean::make('Active', 'is_active')->default(true),
        ];
    }
}
