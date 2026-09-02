<?php

namespace Tests\Feature;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Ward;
use App\Nova\Actions\DischargePatient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

class DischargeActionTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmittedAdmission(): Admission
    {
        $department = Department::create(['name' => 'Medical', 'code' => 'MED'.rand(100, 999), 'is_active' => true]);
        $doctor = Doctor::create([
            'department_id' => $department->id, 'name' => 'Dr. Ward',
            'practice_number' => 'MP'.rand(1000, 9999), 'is_active' => true,
        ]);
        $patient = Patient::create([
            'first_name' => 'Ward', 'last_name' => 'Patient', 'date_of_birth' => '1980-01-01',
            'gender' => 'male', 'phone' => '+27000000000', 'patient_type' => 'non_staff', 'billing_type' => 'cash',
        ]);
        $ward = Ward::create(['name' => 'Ward A', 'department_id' => $department->id, 'type' => 'general', 'capacity' => 10, 'is_active' => true]);
        $bed = Bed::create(['ward_id' => $ward->id, 'bed_number' => 'A'.rand(1, 99), 'status' => 'available']);

        return Admission::create([
            'patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'department_id' => $department->id,
            'ward_id' => $ward->id, 'bed_id' => $bed->id, 'admitted_at' => now()->subDays(3),
            'reason_for_admission' => 'Observation', 'status' => 'admitted',
        ]);
    }

    private function fields(array $values): ActionFields
    {
        return new ActionFields(new Collection($values), new Collection());
    }

    public function test_discharge_action_updates_status_notes_and_frees_bed(): void
    {
        $admission = $this->makeAdmittedAdmission();
        $bedId = $admission->bed_id;

        $this->assertSame('occupied', Bed::find($bedId)->status->value);

        (new DischargePatient)->handle(
            $this->fields([
                'status' => 'discharged',
                'discharged_at' => now(),
                'discharge_notes' => 'Recovered well. Follow up in 2 weeks.',
            ]),
            new Collection([$admission])
        );

        $admission->refresh();

        $this->assertSame(AdmissionStatus::Discharged, $admission->status);
        $this->assertNotNull($admission->discharged_at);
        $this->assertSame('Recovered well. Follow up in 2 weeks.', $admission->discharge_notes);
        $this->assertSame('available', Bed::find($bedId)->status->value);
    }

    public function test_discharge_action_rejects_already_discharged_admission(): void
    {
        $admission = $this->makeAdmittedAdmission();
        $admission->update(['status' => 'discharged']);

        $result = (new DischargePatient)->handle(
            $this->fields(['status' => 'discharged', 'discharged_at' => now(), 'discharge_notes' => 'x']),
            new Collection([$admission->fresh()])
        );

        // Returns a danger message payload rather than re-processing.
        $this->assertNotNull($result);
    }
}
