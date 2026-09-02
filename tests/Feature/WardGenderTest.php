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

class WardGenderTest extends TestCase
{
    use RefreshDatabase;

    private Department $department;
    private Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->department = Department::create(['name' => 'Med', 'code' => 'MD'.rand(100, 999), 'is_active' => true]);
        $this->doctor = Doctor::create([
            'department_id' => $this->department->id, 'name' => 'Dr. X',
            'practice_number' => 'MP'.rand(1000, 9999), 'is_active' => true,
        ]);
    }

    private function patient(string $gender): Patient
    {
        return Patient::create([
            'first_name' => ucfirst($gender), 'last_name' => 'Test', 'date_of_birth' => '1990-01-01',
            'gender' => $gender, 'phone' => '+270000'.rand(1000, 9999),
            'patient_type' => 'non_staff', 'billing_type' => 'cash',
        ]);
    }

    private function ward(string $type, ?string $restriction = null): Ward
    {
        return Ward::create([
            'name' => ucfirst($type).' Ward', 'department_id' => $this->department->id,
            'type' => $type, 'gender_restriction' => $restriction, 'capacity' => 10, 'is_active' => true,
        ]);
    }

    private function admit(Patient $patient, Ward $ward): Admission
    {
        return Admission::create([
            'patient_id' => $patient->id, 'doctor_id' => $this->doctor->id,
            'department_id' => $this->department->id, 'ward_id' => $ward->id,
            'admitted_at' => now(), 'reason_for_admission' => 'Test', 'status' => 'admitted',
        ]);
    }

    public function test_male_cannot_be_admitted_to_female_ward(): void
    {
        $this->expectException(ValidationException::class);
        $this->admit($this->patient('male'), $this->ward('general', 'female'));
    }

    public function test_female_cannot_be_admitted_to_male_ward(): void
    {
        $this->expectException(ValidationException::class);
        $this->admit($this->patient('female'), $this->ward('general', 'male'));
    }

    public function test_male_cannot_be_admitted_to_maternity_ward(): void
    {
        $this->expectException(ValidationException::class);
        // Maternity is implicitly female-only regardless of the restriction column.
        $this->admit($this->patient('male'), $this->ward('maternity'));
    }

    public function test_female_can_be_admitted_to_maternity_ward(): void
    {
        $admission = $this->admit($this->patient('female'), $this->ward('maternity'));
        $this->assertSame('admitted', $admission->status->value);
    }

    public function test_any_gender_allowed_in_unrestricted_ward(): void
    {
        $male = $this->admit($this->patient('male'), $this->ward('general'));
        $female = $this->admit($this->patient('female'), $this->ward('icu'));

        $this->assertSame('admitted', $male->status->value);
        $this->assertSame('admitted', $female->status->value);
    }
}
