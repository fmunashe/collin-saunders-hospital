<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Ward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InpatientTest extends TestCase
{
    use RefreshDatabase;

    private array $context = [];

    private function context(): array
    {
        if ($this->context) {
            return $this->context;
        }

        $department = Department::create(['name' => 'Medicine', 'code' => 'MED', 'is_active' => true]);
        $doctor = Doctor::create([
            'department_id' => $department->id,
            'name' => 'Dr. Ward',
            'practice_number' => 'MP'.rand(1000, 9999),
            'is_active' => true,
        ]);
        $ward = Ward::create([
            'department_id' => $department->id,
            'name' => 'General Ward',
            'type' => 'general',
            'capacity' => 10,
            'is_active' => true,
        ]);
        $bed = Bed::create([
            'ward_id' => $ward->id,
            'bed_number' => 'GW-01',
            'status' => 'available',
        ]);

        return $this->context = compact('department', 'doctor', 'ward', 'bed');
    }

    private function makePatient(string $first = 'John'): Patient
    {
        return Patient::create([
            'first_name' => $first,
            'last_name' => 'Doe',
            'date_of_birth' => '1985-05-05',
            'gender' => 'male',
            'phone' => '+27000000000',
            'patient_type' => 'non_staff',
            'billing_type' => 'cash',
        ]);
    }

    private function admit(Patient $patient, string $bedId): Admission
    {
        $c = $this->context();

        return Admission::create([
            'patient_id' => $patient->id,
            'doctor_id' => $c['doctor']->id,
            'department_id' => $c['department']->id,
            'ward_id' => $c['ward']->id,
            'bed_id' => $bedId,
            'admitted_at' => now(),
            'reason_for_admission' => 'Observation',
            'status' => 'admitted',
        ]);
    }

    public function test_admitting_a_patient_occupies_the_bed(): void
    {
        $c = $this->context();
        $this->admit($this->makePatient(), $c['bed']->id);

        $this->assertEquals('occupied', $c['bed']->fresh()->status->value);
    }

    public function test_discharging_a_patient_frees_the_bed(): void
    {
        $c = $this->context();
        $admission = $this->admit($this->makePatient(), $c['bed']->id);
        $this->assertEquals('occupied', $c['bed']->fresh()->status->value);

        $admission->update(['status' => 'discharged']);

        $this->assertEquals('available', $c['bed']->fresh()->status->value);
        $this->assertNotNull($admission->fresh()->discharged_at);
        // The bed is also released from the admission (no longer allocated).
        $this->assertNull($admission->fresh()->bed_id);
    }

    public function test_cannot_double_book_an_occupied_bed(): void
    {
        $c = $this->context();
        $this->admit($this->makePatient('First'), $c['bed']->id);

        $this->expectException(ValidationException::class);

        $this->admit($this->makePatient('Second'), $c['bed']->id);
    }

    public function test_bed_can_be_reused_after_discharge(): void
    {
        $c = $this->context();
        $first = $this->admit($this->makePatient('First'), $c['bed']->id);
        $first->update(['status' => 'discharged']);

        $second = $this->admit($this->makePatient('Second'), $c['bed']->id);

        $this->assertEquals('admitted', $second->fresh()->status->value);
        $this->assertEquals('occupied', $c['bed']->fresh()->status->value);
    }

    public function test_patient_is_flagged_as_inpatient_when_admitted(): void
    {
        $c = $this->context();
        $patient = $this->makePatient();
        $this->admit($patient, $c['bed']->id);

        $this->assertTrue($patient->fresh()->isInpatient());
    }
}
