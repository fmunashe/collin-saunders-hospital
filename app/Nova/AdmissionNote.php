<?php

namespace App\Nova;

use App\Enums\AdmissionNoteType;
use App\Rules\AdmissionIsActive;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class AdmissionNote extends Resource
{
    public static $model = \App\Models\AdmissionNote::class;

    public static $title = 'id';

    public static $tableStyle = 'tight';

    public static $displayInNavigation = true;

    public static $search = ['id', 'note'];

    public static function label(): string
    {
        return 'Admission Notes';
    }

    public static function singularLabel(): string
    {
        return 'Admission Note';
    }

    public static function searchableColumns(): array
    {
        return ['id', 'note', 'admission.patient.patient_number', 'admission.patient.first_name', 'admission.patient.last_name'];
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->onlyOnDetail(),

            // Only allow attaching notes to currently-admitted patients.
            BelongsTo::make('Admission')
                ->searchable()
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $query->where('status', 'admitted');
                })
                ->rules('required', new AdmissionIsActive),

            Badge::make('Type')->map([
                'doctor' => 'info',
                'nurse' => 'success',
                'observation' => 'warning',
                'procedure' => 'danger',
                'general' => 'info',
            ])->exceptOnForms(),

            Select::make('Type')
                ->options(collect(AdmissionNoteType::cases())->mapWithKeys(fn ($t) => [$t->value => ucfirst($t->value)]))
                ->default('general')
                ->rules('required')
                ->searchable()
                ->displayUsingLabels()
                ->onlyOnForms(),

            DateTime::make('Noted At')->rules('required')->sortable()->default(now()),

            // Author is stamped automatically from the logged-in user on create.
            BelongsTo::make('Author', 'author', User::class)
                ->nullable()
                ->exceptOnForms(),

            Text::make('Note', fn () => str($this->note)->limit(60))->onlyOnIndex(),

            Textarea::make('Note')->rules('required')->alwaysShow()->hideFromIndex(),
        ];
    }

    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [5, 10, 15, 25, 50, 100];
}
