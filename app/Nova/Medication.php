<?php

namespace App\Nova;

use App\Nova\Actions\AdjustStock;
use App\Nova\Actions\ReceiveStock;
use App\Nova\Filters\MedicationExpiryStatus;
use App\Nova\Filters\MedicationStockStatus;
use App\Nova\Metrics\ExpiringMedications;
use App\Nova\Metrics\LowStockMedications;
use App\Nova\Metrics\OutOfStockMedications;
use App\Nova\Metrics\TotalMedications;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Medication extends Resource
{
    public static $model = \App\Models\Medication::class;

    public static $title = 'name';
    public static $tableStyle = 'tight';

    public static $search = ['id', 'name', 'generic_name'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Generic Name')->nullable()->sortable(),
            Text::make('Dosage Form')->rules('required', 'max:100'),
            Text::make('Strength')->rules('required', 'max:100'),
            Number::make('Stock Quantity')->sortable()->rules('required', 'integer', 'min:0'),
            Number::make('Reorder Level')->rules('required', 'integer', 'min:0')->hideFromIndex(),
            Currency::make('Unit Price')->rules('required', 'numeric', 'min:0'),
            Date::make('Expiry Date')->nullable()->sortable(),
            Boolean::make('Active', 'is_active')->default(true),
            Badge::make('Stock', fn () => $this->stockStatusLabel())->map([
                'In Stock' => 'success',
                'Low Stock' => 'warning',
                'Out of Stock' => 'danger',
            ])->exceptOnForms(),
            Badge::make('Expiry', fn () => $this->expiryStatusLabel())->map([
                'Valid' => 'success',
                'No Expiry' => 'info',
                'Expiring Soon' => 'warning',
                'Expired' => 'danger',
            ])->exceptOnForms(),
            HasMany::make('Stock Movements', 'stockMovements', StockMovement::class),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            (new TotalMedications)->width('1/4'),
            (new LowStockMedications)->width('1/4'),
            (new OutOfStockMedications)->width('1/4'),
            (new ExpiringMedications)->width('1/4'),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new MedicationStockStatus,
            new MedicationExpiryStatus,
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return array_merge(parent::actions($request), [
            new ReceiveStock,
            new AdjustStock,
        ]);
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
