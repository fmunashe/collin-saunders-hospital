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
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class ReceiveStock extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Receive Stock';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $quantity = (int) $fields->get('quantity');
        $reference = $fields->get('reference');
        $notes = $fields->get('notes');
        $processed = [];

        foreach ($models as $medication) {
            $stockBefore = $medication->stock_quantity;

            $medication->increment('stock_quantity', $quantity);

            StockMovement::create([
                'medication_id' => $medication->id,
                'user_id' => auth()->id(),
                'type' => 'received',
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockBefore + $quantity,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            $processed[] = $medication->name;
        }

        $count = count($processed);

        if ($count === 1) {
            return Action::message("Received {$quantity} units of {$processed[0]}.");
        }

        return Action::message("Received {$quantity} units each for {$count} medications.");
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Number::make('Quantity')->rules('required', 'integer', 'min:1')->help('This quantity will be added to each selected medication'),
            Text::make('Reference', 'reference')->help('e.g. PO number, supplier invoice')->nullable(),
            Textarea::make('Notes')->nullable(),
        ];
    }
}
