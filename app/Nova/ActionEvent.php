<?php

namespace App\Nova;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActionEvent extends Resource
{
    public static $model = \Laravel\Nova\Actions\ActionEvent::class;

    public static $title = 'name';

    public static $search = ['id', 'name', 'actionable_type', 'batch_id'];

    public static $tableStyle = 'tight';
    public static $globallySearchable = false;

    public static function label(): string
    {
        return 'Audit Log';
    }

    public static function singularLabel(): string
    {
        return 'Audit Log Entry';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Action', 'name')->sortable(),
            Text::make('User', function () {
                return $this->user?->name ?? 'System';
            })->sortable(),
            Text::make('Resource Type', function () {
                $type = $this->actionable_type;
                return $type ? class_basename($type) : '-';
            })->sortable(),
            Text::make('Resource ID', 'actionable_id')->onlyOnDetail(),
            Text::make('Status')->sortable()->filterable(),
            Text::make('Batch ID', 'batch_id')->onlyOnDetail(),
            DateTime::make('Created At')->sortable(),
        ];
    }

    public static function authorizedToCreate($request): bool
    {
        return false;
    }

    public function authorizedToUpdate($request): bool
    {
        return false;
    }

    public function authorizedToDelete($request): bool
    {
        return false;
    }

    public function authorizedToReplicate($request): bool
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
