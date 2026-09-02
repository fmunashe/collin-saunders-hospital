<?php

namespace App\Nova\Actions;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class DischargePatient extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Discharge Patient';

    public $showInline = true;

    /** Confirmation button/heading text on the modal. */
    public $confirmButtonText = 'Discharge';

    public $confirmText = 'Confirm the discharge details below. This will free the assigned bed.';

    public function handle(ActionFields $fields, Collection $models): mixed
    {
        // This action operates on a single admission at a time.
        if ($models->count() > 1) {
            return Action::danger('Please discharge one admission at a time.');
        }

        /** @var Admission $admission */
        $admission = $models->first();

        if ($admission->status !== AdmissionStatus::Admitted) {
            return Action::danger('This admission is not active — the patient has already been discharged, transferred, or recorded as deceased.');
        }

        $status = $fields->get('status') ?: AdmissionStatus::Discharged->value;
        $dischargedAt = $fields->get('discharged_at') ?: now();

        // Append rather than overwrite any existing discharge notes.
        $notes = trim((string) $fields->get('discharge_notes'));

        $admission->update([
            'status' => $status,
            'discharged_at' => $dischargedAt,
            'discharge_notes' => $notes !== '' ? $notes : $admission->discharge_notes,
        ]);
        // The Admission model's saving/updated hooks free the bed automatically.

        $label = ucfirst($status);
        $patient = $admission->patient;
        $who = $patient ? "{$patient->patient_number} — {$patient->first_name} {$patient->last_name}" : 'Patient';

        return Action::message("{$who} has been {$label} and the bed has been released.");
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Select::make('Outcome', 'status')
                ->options([
                    AdmissionStatus::Discharged->value => 'Discharged',
                    AdmissionStatus::Transferred->value => 'Transferred',
                    AdmissionStatus::Deceased->value => 'Deceased',
                ])
                ->default(AdmissionStatus::Discharged->value)
                ->rules('required')
                ->searchable()
                ->displayUsingLabels(),

            DateTime::make('Discharged At', 'discharged_at')
                ->default(now())
                ->rules('required'),

            Textarea::make('Discharge Notes', 'discharge_notes')
                ->rules('required')
                ->help('Summary of the stay, outcome, and any follow-up instructions.'),
        ];
    }
}
