<?php

namespace App\Nova;

use App\Models\Permission as PermissionModel;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Role extends Resource
{
    public static $model = \App\Models\Role::class;

    public static $title = 'name';

    public static $tableStyle = 'tight';

    public static $search = ['id', 'name'];

    public static $group = 'Access Control';

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Guard Name')->default('web')->rules('required'),

            // Attach many permissions at once. Each permission is a checkbox, so
            // you can tick any number of them and they are all synced in one save,
            // instead of the one-at-a-time BelongsToMany "Attach" screen.
            BooleanGroup::make('Permissions')
                ->options(PermissionModel::orderBy('name')->pluck('name', 'name')->toArray())
                ->resolveUsing(fn () => $this->permissions->pluck('name')
                    ->mapWithKeys(fn ($name) => [$name => true])
                    ->all())
                ->fillUsing(function (NovaRequest $request, $model, $attribute, $requestAttribute) {
                    $decoded = json_decode($request->input($requestAttribute) ?? '{}', true) ?: [];

                    // Keep only the permission names that are toggled on.
                    $selected = collect($decoded)
                        ->filter(fn ($enabled) => $enabled === true)
                        ->keys()
                        ->all();

                    // Sync after the role is saved (needs an id).
                    return function () use ($model, $selected) {
                        $model->syncPermissions($selected);
                        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                    };
                })
                ->onlyOnForms(),

            // Read-only view of attached permissions on index/detail.
            BelongsToMany::make('Permissions', 'permissions', Permission::class),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
