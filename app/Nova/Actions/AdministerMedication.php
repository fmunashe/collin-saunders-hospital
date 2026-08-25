<?php

namespace App\Nova\Actions;

use App\Models\Admission;
use App\Models\Medication;
use App\Models\MedicationAdministration;
use App\Models\StockMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class AdministerMedication extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Administer Medication';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        /** @var Admission $admission */
        $admission = $models->first();

        if ($admission->status->value !== 'admitted') {
            return Action::danger('Cannot administer medication to a discharged patient.');
        }

        $medication = Medication::find($fields->get('medication_id'));

        if (! $medication) {
            return Action::danger('Medication not found.');
        }

        if ($medication->stock_quantity < 1) {
            return Action::danger("{$medication->name} is out of stock.");
        }

        // Record the administration
        MedicationAdministration::create([
            'admission_id' => $admission->id,
            'medication_id' => $medication->id,
            'administered_by' => auth()->id(),
            'dosage' => $fields->get('dosage'),
            'route' => $fields->get('route'),
            'administered_at' => $fields->get('administered_at') ?? now(),
            'status' => 'administered',
            'notes' => $fields->get('notes'),
        ]);

        // Deduct from stock
        $stockBefore = $medication->stock_quantity;
        $medication->decrement('stock_quantity');

        StockMovement::create([
            'medication_id' => $medication->id,
            'user_id' => auth()->id(),
            'type' => 'dispensed',
            'quantity' => -1,
            'stock_before' => $stockBefore,
            'stock_after' => $stockBefore - 1,
            'reference' => 'Admission #' . $admission->id,
            'notes' => "Administered to patient: {$fields->get('dosage')} via {$fields->get('route')}",
        ]);

        return Action::message("{$medication->name} ({$fields->get('dosage')}) administered successfully.");
    }

    public function fields(NovaRequest $request): array
    {
        $medications = Medication::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return [
            Select::make('Medication', 'medication_id')->options($medications)->rules('required')->searchable(),
            Text::make('Dosage')->rules('required', 'max:100')->help('e.g. 500mg, 10ml, 2 tablets'),
            Select::make('Route')->options([
                'oral' => 'Oral',
                'iv' => 'IV (Intravenous)',
                'im' => 'IM (Intramuscular)',
                'sc' => 'SC (Subcutaneous)',
                'topical' => 'Topical',
                'inhalation' => 'Inhalation',
                'rectal' => 'Rectal',
                'sublingual' => 'Sublingual',
            ])->rules('required'),
            DateTime::make('Administered At')->default(now()),
            Textarea::make('Notes')->nullable(),
        ];
    }
}
