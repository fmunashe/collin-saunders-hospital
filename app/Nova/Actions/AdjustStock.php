<?php

namespace App\Nova\Actions;

use App\Models\Medication;
use App\Models\StockMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class AdjustStock extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Stock Count Adjustment';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $actualCount = (int) $fields->get('actual_count');
        $reason = $fields->get('reason');
        $processed = [];

        foreach ($models as $medication) {
            $stockBefore = $medication->stock_quantity;
            $difference = $actualCount - $stockBefore;

            $medication->update(['stock_quantity' => $actualCount]);

            StockMovement::create([
                'medication_id' => $medication->id,
                'user_id' => auth()->id(),
                'type' => 'adjustment',
                'quantity' => $difference,
                'stock_before' => $stockBefore,
                'stock_after' => $actualCount,
                'reference' => 'Stock count',
                'notes' => $reason,
            ]);

            $processed[] = $medication->name;
        }

        $count = count($processed);

        if ($count === 1) {
            $medication = $models->first();
            $stockBefore = $medication->getOriginal('stock_quantity') ?? $actualCount;
            $direction = ($actualCount - $stockBefore) >= 0 ? '+' . ($actualCount - $stockBefore) : (string) ($actualCount - $stockBefore);

            return Action::message("{$processed[0]}: stock set to {$actualCount}.");
        }

        return Action::message("Stock count set to {$actualCount} for {$count} medications.");
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Number::make('Actual Count', 'actual_count')->rules('required', 'integer', 'min:0')->help('Enter the physical count. When applied to multiple items, all will be set to this value.'),
            Textarea::make('Reason', 'reason')->rules('required')->help('Reason for adjustment (e.g. stock count, breakage, theft)'),
        ];
    }
}
