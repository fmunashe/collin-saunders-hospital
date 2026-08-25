<?php

namespace App\Nova\Actions;

use App\Models\PrescriptionItem;
use App\Models\StockMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class BulkDispense extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Dispense Selected';

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $dispensed = 0;
        $skipped = 0;
        $outOfStock = [];

        foreach ($models as $item) {
            // Skip already dispensed items
            if ($item->dispensed) {
                $skipped++;
                continue;
            }

            $medication = $item->medication;

            // Check stock
            if ($medication->stock_quantity < $item->quantity) {
                $outOfStock[] = $medication->name;
                continue;
            }

            // Mark as dispensed
            $stockBefore = $medication->stock_quantity;
            $medication->decrement('stock_quantity', $item->quantity);

            StockMovement::create([
                'medication_id' => $medication->id,
                'user_id' => auth()->id(),
                'type' => 'dispensed',
                'quantity' => -$item->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockBefore - $item->quantity,
                'reference' => 'Prescription #' . $item->prescription_id,
                'notes' => 'Bulk dispensed via pharmacy',
            ]);

            $item->updateQuietly(['dispensed' => true]);
            $dispensed++;
        }

        $messages = [];
        if ($dispensed > 0) {
            $messages[] = "{$dispensed} item(s) dispensed";
        }
        if ($skipped > 0) {
            $messages[] = "{$skipped} already dispensed";
        }
        if (count($outOfStock) > 0) {
            $messages[] = "Insufficient stock: " . implode(', ', $outOfStock);
        }

        $message = implode('. ', $messages) . '.';

        if (count($outOfStock) > 0) {
            return Action::danger($message);
        }

        return Action::message($message);
    }

    public function fields(NovaRequest $request): array
    {
        return [];
    }
}
