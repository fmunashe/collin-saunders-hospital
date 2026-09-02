<?php

namespace App\Nova;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Setting extends Resource
{
    public static $model = \App\Models\Setting::class;

    public static $title = 'label';

    public static $tableStyle = 'tight';

    public static $search = ['id', 'key', 'label', 'group'];

    public static $group = 'Configuration';

    public static function label(): string
    {
        return 'Settings';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),

            Text::make('Group')->sortable()->exceptOnForms(),
            Text::make('Label')->sortable()->rules('required', 'max:255'),
            Text::make('Key')->readonly()->hideFromIndex()
                ->help('System key — do not change; it maps to a configuration value.'),
            Text::make('Type')->exceptOnForms(),

            // Value is the one field admins normally edit. Rendered per type so
            // the input is appropriate (number / decimal / toggle / text).
            $this->valueField(),

            Textarea::make('Description')->nullable()->hideFromIndex(),
        ];
    }

    /**
     * Build the value input appropriate to the setting's declared type.
     */
    private function valueField()
    {
        $type = $this->resource->type ?? 'string';

        return match ($type) {
            // Monetary settings (fees, ward rates) show the USD currency symbol.
            'decimal' => Currency::make('Value')
                ->currency('USD')
                ->rules('required', 'numeric', 'min:0'),
            'integer' => Number::make('Value')->rules('required', 'integer', 'min:0'),
            'boolean' => Boolean::make('Value')
                ->resolveUsing(fn ($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN))
                ->fillUsing(fn ($request, $model, $attr, $reqAttr) => $model->value = $request->boolean($reqAttr) ? '1' : '0'),
            default => Text::make('Value')->rules('required'),
        };
    }

    /**
     * Settings are seeded, not created ad-hoc, and never deleted from the UI.
     */
    public static function authorizedToCreate(\Illuminate\Http\Request $request): bool
    {
        return false;
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [10, 25, 50, 100];
}
