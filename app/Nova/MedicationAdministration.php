<?php

namespace App\Nova;

use App\Enums\AdministrationRoute;
use App\Enums\AdministrationStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class MedicationAdministration extends Resource
{
    public static $model = \App\Models\MedicationAdministration::class;

    public static $title = 'id';

    public static $tableStyle = 'tight';

    public static $search = ['id'];

    public static $displayInNavigation = false;

    public static function label(): string
    {
        return 'Medication Administrations';
    }

    public static function singularLabel(): string
    {
        return 'Medication Administration';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Admission')->hideFromIndex(),
            BelongsTo::make('Medication')->searchable()->rules('required'),
            Text::make('Dosage')->rules('required', 'max:100')->help('e.g. 500mg, 10ml, 2 tablets'),
            Select::make('Route')->options(collect(AdministrationRoute::cases())->mapWithKeys(fn ($r) => [$r->value => strtoupper($r->value)]))->rules('required')->displayUsingLabels(),
            DateTime::make('Administered At')->rules('required')->default(now()),
            DateTime::make('Scheduled At')->nullable()->hideFromIndex(),
            BelongsTo::make('Administered By', 'administeredBy', User::class)->nullable()->default(auth()->id())->hideFromIndex(),
            Select::make('Status')->options(collect(AdministrationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))->default('administered')->rules('required')->displayUsingLabels(),
            Textarea::make('Notes')->nullable()->hideFromIndex(),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
