<?php

namespace App\Nova;

use App\Enums\StockMovementType;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class StockMovement extends Resource
{
    public static $model = \App\Models\StockMovement::class;

    public static $title = 'id';

    public static $tableStyle = 'tight';

    public static $search = ['id', 'reference', 'notes'];

    public static $displayInNavigation = true;

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            BelongsTo::make('Medication')->searchable(),
            BelongsTo::make('User')->nullable(),
            Select::make('Type')->options(collect(StockMovementType::cases())->mapWithKeys(fn ($t) => [$t->value => ucfirst($t->value)]))->searchable()->displayUsingLabels(),
            Number::make('Quantity')->sortable(),
            Number::make('Stock Before'),
            Number::make('Stock After'),
            Text::make('Reference')->nullable(),
            Textarea::make('Notes')->nullable()->hideFromIndex(),
            DateTime::make('Created At')->sortable()->hideWhenCreating()->hideWhenUpdating(),
        ];
    }

    public static function authorizedToCreate($request): bool
    {
        return false; // Stock movements are created via actions only
    }

    public function authorizedToUpdate($request): bool
    {
        return false;
    }

    public function authorizedToDelete($request): bool
    {
        return false;
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
