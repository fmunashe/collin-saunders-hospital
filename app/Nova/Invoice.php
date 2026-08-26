<?php

namespace App\Nova;

use App\Enums\BillingType;
use App\Enums\InvoiceStatus;
use App\Nova\Actions\DownloadInvoice;
use App\Nova\Filters\InvoiceNumber;
use App\Nova\Filters\InvoicePatientNumber;
use App\Nova\Filters\InvoicePaymentMethod;
use App\Nova\Filters\InvoiceStatus as InvoiceStatusFilter;
use App\Nova\Metrics\InvoicesByPaymentMethod;
use App\Nova\Metrics\InvoicesByStatus as InvoicesByStatusMetric;
use App\Nova\Metrics\RevenuePerDay;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Invoice extends Resource
{
    public static $model = \App\Models\Invoice::class;

    public static $title = 'invoice_number';
    public static $tableStyle = 'tight';

    public static $search = ['id', 'invoice_number'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),
            Text::make('Invoice Number')->sortable()->rules('required')->creationRules('unique:invoices,invoice_number')->updateRules('unique:invoices,invoice_number,{{resourceId}}'),
            BelongsTo::make('Patient')->searchable()->rules('required'),
            BelongsTo::make('Visit')->nullable(),
            BelongsTo::make('Admission')->nullable(),
            Currency::make('Total Amount')->sortable()->rules('required'),
            Currency::make('Paid Amount')->sortable()->default(0),
            Currency::make('Balance', fn () => $this->balance)->onlyOnDetail(),
            Select::make('Payment Method')->options(collect(BillingType::cases())->mapWithKeys(fn ($t) => [$t->value => str_replace('_', ' ', ucfirst($t->value))]))->rules('required')->searchable()->displayUsingLabels(),
            Select::make('Status')->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]))->default('pending')->rules('required')->searchable()->displayUsingLabels(),
            Textarea::make('Notes')->nullable()->hideFromIndex(),
            HasMany::make('Items', 'items', InvoiceItem::class),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            (new InvoicesByStatusMetric)->width('1/2'),
            (new InvoicesByPaymentMethod)->width('1/2'),
            (new RevenuePerDay)->width('full'),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new InvoicePatientNumber,
            new InvoiceNumber,
            new InvoicePaymentMethod,
            new InvoiceStatusFilter,
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return array_merge(parent::actions($request), [
            new DownloadInvoice,
        ]);
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
