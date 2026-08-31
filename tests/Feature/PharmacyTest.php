<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Department;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PharmacyTest extends TestCase
{
    use RefreshDatabase;

    private function makePrescriptionItem(int $stock, int $quantity, ?string $expiry = '2035-01-01'): PrescriptionItem
    {
        $department = Department::create(['name' => 'Pharmacy', 'code' => 'PHR', 'is_active' => true]);
        $doctor = Doctor::create([
            'department_id' => $department->id,
            'name' => 'Dr. Test',
            'practice_number' => 'MP'.rand(1000, 9999),
            'is_active' => true,
        ]);
        $patient = Patient::create([
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'phone' => '+27000000000',
            'patient_type' => 'non_staff',
            'billing_type' => 'cash',
        ]);
        $medication = Medication::create([
            'name' => 'TestMed',
            'dosage_form' => 'Tablet',
            'strength' => '10mg',
            'stock_quantity' => $stock,
            'reorder_level' => 5,
            'unit_price' => 2.00,
            'expiry_date' => $expiry,
            'is_active' => true,
        ]);
        $prescription = Prescription::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'prescribed_at' => now(),
            'status' => 'pending',
        ]);

        return PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medication_id' => $medication->id,
            'dosage' => '1 daily',
            'quantity' => $quantity,
            'dispensed' => false,
        ]);
    }

    public function test_dispensing_deducts_stock_and_records_movement(): void
    {
        $item = $this->makePrescriptionItem(stock: 100, quantity: 20);

        $item->update(['dispensed' => true]);

        $this->assertEquals(80, $item->medication->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'medication_id' => $item->medication_id,
            'type' => 'dispensed',
            'quantity' => -20,
        ]);
    }

    public function test_reversing_dispense_restores_stock(): void
    {
        $item = $this->makePrescriptionItem(stock: 100, quantity: 20);
        $item->update(['dispensed' => true]);
        $this->assertEquals(80, $item->medication->fresh()->stock_quantity);

        $item->update(['dispensed' => false]);

        $this->assertEquals(100, $item->medication->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'medication_id' => $item->medication_id,
            'type' => 'returned',
            'quantity' => 20,
        ]);
    }

    public function test_cannot_dispense_more_than_available_stock(): void
    {
        $item = $this->makePrescriptionItem(stock: 5, quantity: 20);

        $this->expectException(ValidationException::class);

        try {
            $item->update(['dispensed' => true]);
        } finally {
            // Stock must be untouched
            $this->assertEquals(5, $item->medication->fresh()->stock_quantity);
        }
    }

    public function test_cannot_dispense_expired_medication(): void
    {
        $item = $this->makePrescriptionItem(stock: 100, quantity: 10, expiry: '2020-01-01');

        $this->expectException(ValidationException::class);

        try {
            $item->update(['dispensed' => true]);
        } finally {
            $this->assertEquals(100, $item->medication->fresh()->stock_quantity);
        }
    }

    public function test_prescription_status_syncs_when_all_items_dispensed(): void
    {
        $item = $this->makePrescriptionItem(stock: 100, quantity: 10);

        $item->update(['dispensed' => true]);

        $this->assertEquals('dispensed', $item->prescription->fresh()->status->value);
        $this->assertNotNull($item->prescription->fresh()->dispensed_at);
    }
}
