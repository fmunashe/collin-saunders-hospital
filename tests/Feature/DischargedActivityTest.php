<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Ward;
use App\Rules\AdmissionIsActive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class DischargedActivityTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmission(string $status): Admission
    {
        $department = Department::create(['name' => 'Medical', 'code' => 'MED'.rand(100, 999), 'is_active' => true]);
        $doctor = Doctor::create([
            'department_id' => $department->id,
            'name' => 'Dr. Ward',
            'practice_number' => 'MP'.rand(1000, 9999),
            'is_active' => true,
        ]);
        $patient = Patient::create([
            'first_name' => 'Ward', 'last_name' => 'Patient', 'date_of_birth' => '1980-01-01',
            'gender' => 'male', 'phone' => '+27000000000', 'patient_type' => 'non_staff', 'billing_type' => 'cash',
        ]);
        $ward = Ward::create(['name' => 'Ward A', 'department_id' => $department->id, 'type' => 'general', 'capacity' => 10, 'is_active' => true]);
        $bed = Bed::create(['ward_id' => $ward->id, 'bed_number' => 'A'.rand(1, 99), 'status' => 'available']);

        return Admission::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'ward_id' => $ward->id,
            'bed_id' => $status === 'admitted' ? $bed->id : null,
            'admitted_at' => now()->subDays(3),
            'discharged_at' => $status === 'admitted' ? null : now(),
            'reason_for_admission' => 'Observation',
            'status' => $status,
        ]);
    }

    private function passes(string $admissionId): bool
    {
        $v = Validator::make(
            ['admission_id' => $admissionId],
            ['admission_id' => [new AdmissionIsActive]]
        );

        return $v->passes();
    }

    public function test_active_admission_is_accepted(): void
    {
        $admission = $this->makeAdmission('admitted');

        $this->assertTrue($this->passes($admission->id));
    }

    public function test_discharged_admission_is_rejected(): void
    {
        $admission = $this->makeAdmission('discharged');

        $this->assertFalse($this->passes($admission->id));
    }

    public function test_transferred_admission_is_rejected(): void
    {
        $admission = $this->makeAdmission('transferred');

        $this->assertFalse($this->passes($admission->id));
    }

    public function test_policies_block_updates_once_admission_discharged(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $user = \App\Models\User::factory()->create();
        $user->assignRole('admin');
        $user = $user->fresh();

        $admission = $this->makeAdmission('admitted');

        // Medication administration on an active admission → allowed to update.
        $medAdmin = \App\Models\MedicationAdministration::create([
            'admission_id' => $admission->id,
            'medication_id' => \App\Models\Medication::create([
                'name' => 'Paracetamol', 'dosage_form' => 'Tablet', 'strength' => '500mg',
                'stock_quantity' => 50, 'reorder_level' => 5, 'unit_price' => 1.0, 'is_active' => true,
            ])->id,
            'dosage' => '500mg', 'route' => 'oral', 'administered_at' => now(), 'status' => 'administered',
        ]);

        $medPolicy = new \App\Policies\MedicationAdministrationPolicy;
        $this->assertTrue($medPolicy->update($user, $medAdmin));

        // Discharge the patient.
        $admission->update(['status' => 'discharged']);
        $medAdmin->refresh();

        // Now updates are blocked by the policy.
        $this->assertFalse($medPolicy->update($user, $medAdmin));
    }
}
