<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Permission extends Resource
{
    public static $model = \App\Models\Permission::class;

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
            BelongsToMany::make('Roles', 'roles', Role::class),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
