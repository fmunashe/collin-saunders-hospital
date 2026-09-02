<?php

namespace App\Nova;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToActionTarget;
use Laravel\Nova\Fields\Status;
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

    /**
     * Use a distinct URI key so this custom resource does NOT collide with
     * Nova's built-in Laravel\Nova\Actions\ActionResource (which also uses
     * "action-events" and defaults per-page to 25). The collision caused this
     * resource's index to be served by Nova's version, ignoring our
     * $perPageOptions. A unique key ensures our resource is used.
     */
    public static function uriKey(): string
    {
        return 'audit-log';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Action', 'name')->sortable(),
            Text::make('User', function () {
                return $this->user?->name ?? 'System';
            })->sortable(),

            // Clickable link to the affected resource (patient, invoice, etc.).
            MorphToActionTarget::make('Resource', 'target'),

            // Native status badge with loading/failed states.
            Status::make('Status', 'status', static function ($value) {
                return $value ? ucfirst($value) : null;
            })->loadingWhen(['Waiting', 'Running'])->failedWhen(['Failed']),

            Text::make('Resource ID', 'actionable_id')->onlyOnDetail(),
            Text::make('Batch ID', 'batch_id')->onlyOnDetail(),
            DateTime::make('Created At')->sortable(),
        ];
    }

    /**
     * Eager-load the user and target so the index renders links efficiently.
     */
    public static function indexQuery(NovaRequest $request, Builder $query): Builder
    {
        return $query->with(['user', 'target']);
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
