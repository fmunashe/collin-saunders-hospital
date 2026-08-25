<?php

namespace App\Nova;

use App\Enums\Gender;
use App\Enums\StaffDesignation;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Staff extends Resource
{
    public static $model = \App\Models\Staff::class;

    public static $title = 'employee_number';

    public static $search = ['id', 'employee_number', 'first_name', 'last_name', 'email'];

    public function subtitle(): ?string
    {
        return "{$this->first_name} {$this->last_name} - {$this->designation?->label()}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Employee Number')->sortable()->rules('required')->creationRules('unique:staff,employee_number')->updateRules('unique:staff,employee_number,{{resourceId}}'),
            Text::make('First Name')->sortable()->rules('required', 'max:255'),
            Text::make('Last Name')->sortable()->rules('required', 'max:255'),
            Text::make('Full Name', fn () => $this->full_name)->onlyOnIndex(),
            Select::make('Designation')->options(collect(StaffDesignation::cases())->mapWithKeys(fn ($d) => [$d->value => $d->label()]))->rules('required')->displayUsingLabels()->filterable(),
            BelongsTo::make('Department')->rules('required'),
            BelongsTo::make('User')->nullable()->searchable(),
            Text::make('Phone')->nullable(),
            Text::make('Email')->nullable()->rules('nullable', 'email'),
            Date::make('Date of Birth')->nullable()->hideFromIndex(),
            Select::make('Gender')->options(collect(Gender::cases())->mapWithKeys(fn ($g) => [$g->value => ucfirst($g->value)]))->nullable()->displayUsingLabels()->hideFromIndex(),
            Date::make('Hire Date')->nullable(),
            Boolean::make('Active', 'is_active')->default(true),
        ];
    }
}
