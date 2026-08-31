<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class ExportResources extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Export';

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $format = $fields->get('format') ?? 'xlsx';
        $timestamp = now()->format('Y-m-d_His');
        $resourceName = class_basename($models->first());
        $filename = "{$resourceName}_export_{$timestamp}.{$format}";

        $data = $models->map(function ($model) {
            return collect($model->getAttributes())
                ->except(['deleted_at'])
                ->toArray();
        });

        $headings = $data->isNotEmpty() ? array_keys($data->first()) : [];

        $export = new class($data, $headings) implements FromCollection, WithHeadings
        {
            public function __construct(
                private Collection $data,
                private array $headings,
            ) {}

            public function collection(): Collection
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        };

        $writerType = match ($format) {
            'csv' => \Maatwebsite\Excel\Excel::CSV,
            default => \Maatwebsite\Excel\Excel::XLSX,
        };

        Excel::store($export, $filename, 'public', $writerType);

        $url = asset("storage/{$filename}");

        return Action::download($url, $filename);
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Select::make('Format')->options([
                'xlsx' => 'Excel (.xlsx)',
                'csv' => 'CSV (.csv)',
            ])->default('xlsx')->rules('required'),
        ];
    }
}
