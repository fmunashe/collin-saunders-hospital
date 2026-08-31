<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalAidDetail;
use App\Models\MedicalAidScheme;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Staff;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        // --- Doctors ---
        $doctors = collect();
        $doctorData = [
            ['name' => 'Dr. James Moyo', 'practice_number' => 'MP0001', 'specialisation' => 'General Practice', 'phone' => '+27 31 500 0001', 'email' => 'james.moyo@hospital.co.za'],
            ['name' => 'Dr. Sarah Ndlovu', 'practice_number' => 'MP0002', 'specialisation' => 'Internal Medicine', 'phone' => '+27 31 500 0002', 'email' => 'sarah.ndlovu@hospital.co.za'],
            ['name' => 'Dr. Peter Mthembu', 'practice_number' => 'MP0003', 'specialisation' => 'Surgery', 'phone' => '+27 31 500 0003', 'email' => 'peter.mthembu@hospital.co.za'],
            ['name' => 'Dr. Grace Chipunza', 'practice_number' => 'MP0004', 'specialisation' => 'Paediatrics', 'phone' => '+27 31 500 0004', 'email' => 'grace.chipunza@hospital.co.za'],
            ['name' => 'Dr. David Sithole', 'practice_number' => 'MP0005', 'specialisation' => 'Orthopaedics', 'phone' => '+27 31 500 0005', 'email' => 'david.sithole@hospital.co.za'],
            ['name' => 'Dr. Linda Maphosa', 'practice_number' => 'MP0006', 'specialisation' => 'Obstetrics & Gynaecology', 'phone' => '+27 31 500 0006', 'email' => 'linda.maphosa@hospital.co.za'],
            ['name' => 'Dr. Thabo Khumalo', 'practice_number' => 'MP0007', 'specialisation' => 'Emergency Medicine', 'phone' => '+27 31 500 0007', 'email' => 'thabo.khumalo@hospital.co.za'],
            ['name' => 'Dr. Nomsa Dube', 'practice_number' => 'MP0008', 'specialisation' => 'Radiology', 'phone' => '+27 31 500 0008', 'email' => 'nomsa.dube@hospital.co.za'],
        ];

        foreach ($doctorData as $i => $doc) {
            $dept = $departments[$i % $departments->count()];
            $doctors->push(Doctor::create(array_merge($doc, ['department_id' => $dept->id])));
        }

        // --- Wards ---
        $wards = collect();
        $wardData = [
            ['name' => 'Male General', 'type' => 'general', 'capacity' => 20],
            ['name' => 'Female General', 'type' => 'general', 'capacity' => 20],
            ['name' => 'ICU', 'type' => 'icu', 'capacity' => 8],
            ['name' => 'Maternity', 'type' => 'maternity', 'capacity' => 15],
            ['name' => 'Paediatric', 'type' => 'paediatric', 'capacity' => 12],
            ['name' => 'Surgical', 'type' => 'general', 'capacity' => 16],
        ];

        foreach ($wardData as $i => $ward) {
            $dept = $departments[$i % $departments->count()];
            $wards->push(Ward::create(array_merge($ward, ['department_id' => $dept->id])));
        }

        // --- Beds ---
        // Beds start available; a few are set to maintenance. Occupancy is
        // driven automatically by admissions (see Admission model events).
        $beds = collect();
        foreach ($wards as $ward) {
            for ($b = 1; $b <= min($ward->capacity, 6); $b++) {
                $status = $b === 6 ? 'maintenance' : 'available';
                $beds->push(Bed::create([
                    'ward_id' => $ward->id,
                    'bed_number' => strtoupper(substr($ward->name, 0, 2)) . '-' . str_pad($b, 2, '0', STR_PAD_LEFT),
                    'status' => $status,
                ]));
            }
        }

        // --- Medications ---
        $medications = collect();
        $medData = [
            ['name' => 'Amoxicillin 500mg', 'generic_name' => 'Amoxicillin', 'dosage_form' => 'Capsule', 'strength' => '500mg', 'stock_quantity' => 500, 'reorder_level' => 50, 'unit_price' => 2.50, 'expiry_date' => '2027-06-15'],
            ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'dosage_form' => 'Tablet', 'strength' => '500mg', 'stock_quantity' => 1000, 'reorder_level' => 100, 'unit_price' => 0.80, 'expiry_date' => '2027-12-01'],
            ['name' => 'Ibuprofen 400mg', 'generic_name' => 'Ibuprofen', 'dosage_form' => 'Tablet', 'strength' => '400mg', 'stock_quantity' => 300, 'reorder_level' => 30, 'unit_price' => 1.20, 'expiry_date' => '2027-09-30'],
            ['name' => 'Metformin 850mg', 'generic_name' => 'Metformin', 'dosage_form' => 'Tablet', 'strength' => '850mg', 'stock_quantity' => 200, 'reorder_level' => 20, 'unit_price' => 3.00, 'expiry_date' => '2028-03-15'],
            ['name' => 'Amlodipine 5mg', 'generic_name' => 'Amlodipine', 'dosage_form' => 'Tablet', 'strength' => '5mg', 'stock_quantity' => 150, 'reorder_level' => 15, 'unit_price' => 4.50, 'expiry_date' => '2028-01-20'],
            ['name' => 'Omeprazole 20mg', 'generic_name' => 'Omeprazole', 'dosage_form' => 'Capsule', 'strength' => '20mg', 'stock_quantity' => 250, 'reorder_level' => 25, 'unit_price' => 5.00, 'expiry_date' => '2027-11-10'],
            ['name' => 'Ciprofloxacin 500mg', 'generic_name' => 'Ciprofloxacin', 'dosage_form' => 'Tablet', 'strength' => '500mg', 'stock_quantity' => 8, 'reorder_level' => 20, 'unit_price' => 6.50, 'expiry_date' => '2027-08-25'],
            ['name' => 'Salbutamol Inhaler', 'generic_name' => 'Salbutamol', 'dosage_form' => 'Inhaler', 'strength' => '100mcg', 'stock_quantity' => 5, 'reorder_level' => 10, 'unit_price' => 45.00, 'expiry_date' => '2027-10-01'],
            ['name' => 'Diclofenac 50mg', 'generic_name' => 'Diclofenac', 'dosage_form' => 'Tablet', 'strength' => '50mg', 'stock_quantity' => 400, 'reorder_level' => 40, 'unit_price' => 1.80, 'expiry_date' => '2028-02-28'],
            ['name' => 'Atorvastatin 20mg', 'generic_name' => 'Atorvastatin', 'dosage_form' => 'Tablet', 'strength' => '20mg', 'stock_quantity' => 180, 'reorder_level' => 20, 'unit_price' => 7.50, 'expiry_date' => '2028-05-15'],
            ['name' => 'Ceftriaxone 1g', 'generic_name' => 'Ceftriaxone', 'dosage_form' => 'Injection', 'strength' => '1g', 'stock_quantity' => 60, 'reorder_level' => 10, 'unit_price' => 35.00, 'expiry_date' => '2027-07-30'],
            ['name' => 'Normal Saline 1L', 'generic_name' => 'Sodium Chloride', 'dosage_form' => 'IV Fluid', 'strength' => '0.9%', 'stock_quantity' => 100, 'reorder_level' => 20, 'unit_price' => 18.00, 'expiry_date' => '2028-06-01'],
        ];

        foreach ($medData as $med) {
            $medications->push(Medication::create($med));
        }

        // --- Patients ---
        $patients = collect();
        $patientData = [
            ['first_name' => 'John', 'last_name' => 'Banda', 'id_number' => '8501015800083', 'date_of_birth' => '1985-01-01', 'gender' => 'male', 'phone' => '+27 72 100 0001', 'email' => 'john.banda@email.com', 'patient_type' => 'non_staff', 'billing_type' => 'cash', 'blood_group' => 'O+'],
            ['first_name' => 'Mary', 'last_name' => 'Ncube', 'id_number' => '9003155800089', 'date_of_birth' => '1990-03-15', 'gender' => 'female', 'phone' => '+27 72 100 0002', 'email' => 'mary.ncube@email.com', 'patient_type' => 'non_staff', 'billing_type' => 'medical_aid', 'blood_group' => 'A+'],
            ['first_name' => 'Sipho', 'last_name' => 'Dlamini', 'id_number' => '7806205800085', 'date_of_birth' => '1978-06-20', 'gender' => 'male', 'phone' => '+27 72 100 0003', 'patient_type' => 'staff', 'billing_type' => 'medical_aid', 'blood_group' => 'B+'],
            ['first_name' => 'Thandi', 'last_name' => 'Zulu', 'id_number' => '9510105800087', 'date_of_birth' => '1995-10-10', 'gender' => 'female', 'phone' => '+27 72 100 0004', 'patient_type' => 'non_staff', 'billing_type' => 'cash', 'blood_group' => 'AB+'],
            ['first_name' => 'Robert', 'last_name' => 'Chikwanda', 'id_number' => '8207125800081', 'date_of_birth' => '1982-07-12', 'gender' => 'male', 'phone' => '+27 72 100 0005', 'patient_type' => 'non_staff', 'billing_type' => 'medical_aid', 'blood_group' => 'O-'],
            ['first_name' => 'Emily', 'last_name' => 'Mahlangu', 'id_number' => '8812225800083', 'date_of_birth' => '1988-12-22', 'gender' => 'female', 'phone' => '+27 72 100 0006', 'patient_type' => 'staff', 'billing_type' => 'medical_aid'],
            ['first_name' => 'Daniel', 'last_name' => 'Ngwenya', 'id_number' => '7504035800089', 'date_of_birth' => '1975-04-03', 'gender' => 'male', 'phone' => '+27 72 100 0007', 'patient_type' => 'non_staff', 'billing_type' => 'cash', 'blood_group' => 'A-'],
            ['first_name' => 'Precious', 'last_name' => 'Mokoena', 'id_number' => '9208185800085', 'date_of_birth' => '1992-08-18', 'gender' => 'female', 'phone' => '+27 72 100 0008', 'patient_type' => 'non_staff', 'billing_type' => 'medical_aid', 'blood_group' => 'B-'],
            ['first_name' => 'Samuel', 'last_name' => 'Mugabe', 'id_number' => '8009095800087', 'date_of_birth' => '1980-09-09', 'gender' => 'male', 'phone' => '+27 72 100 0009', 'patient_type' => 'non_staff', 'billing_type' => 'cash'],
            ['first_name' => 'Nomvula', 'last_name' => 'Shabalala', 'id_number' => '9706285800081', 'date_of_birth' => '1997-06-28', 'gender' => 'female', 'phone' => '+27 72 100 0010', 'patient_type' => 'staff', 'billing_type' => 'medical_aid', 'blood_group' => 'O+'],
            ['first_name' => 'Moses', 'last_name' => 'Tshuma', 'id_number' => '7002145800083', 'date_of_birth' => '1970-02-14', 'gender' => 'male', 'phone' => '+27 72 100 0011', 'patient_type' => 'non_staff', 'billing_type' => 'cash', 'blood_group' => 'A+'],
            ['first_name' => 'Beauty', 'last_name' => 'Chauke', 'id_number' => '9401305800089', 'date_of_birth' => '1994-01-30', 'gender' => 'female', 'phone' => '+27 72 100 0012', 'patient_type' => 'non_staff', 'billing_type' => 'medical_aid'],
        ];

        foreach ($patientData as $p) {
            $patients->push(Patient::create($p));
        }

        // --- Medical Aid Details for medical_aid patients ---
        $schemes = MedicalAidScheme::all();
        $medAidPatients = $patients->filter(fn ($p) => $p->billing_type->value === 'medical_aid');

        foreach ($medAidPatients as $i => $patient) {
            MedicalAidDetail::create([
                'patient_id' => $patient->id,
                'medical_aid_scheme_id' => $schemes[$i % $schemes->count()]->id,
                'membership_number' => 'MEM' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'plan_name' => collect(['Classic', 'Essential', 'Comprehensive', 'Premium'])->random(),
                'main_member_name' => $patient->first_name . ' ' . $patient->last_name,
                'dependency_code' => '00',
                'valid_from' => '2026-01-01',
                'valid_until' => '2026-12-31',
            ]);
        }

        // --- Staff ---
        $staffData = [
            ['first_name' => 'Agnes', 'last_name' => 'Phiri', 'designation' => 'nurse', 'phone' => '+27 72 200 0001', 'email' => 'agnes.phiri@hospital.co.za', 'gender' => 'female'],
            ['first_name' => 'Bongani', 'last_name' => 'Nkosi', 'designation' => 'nurse', 'phone' => '+27 72 200 0002', 'email' => 'bongani.nkosi@hospital.co.za', 'gender' => 'male'],
            ['first_name' => 'Catherine', 'last_name' => 'Mutasa', 'designation' => 'pharmacist', 'phone' => '+27 72 200 0003', 'email' => 'catherine.mutasa@hospital.co.za', 'gender' => 'female'],
            ['first_name' => 'Dennis', 'last_name' => 'Sibanda', 'designation' => 'lab_technician', 'phone' => '+27 72 200 0004', 'email' => 'dennis.sibanda@hospital.co.za', 'gender' => 'male'],
            ['first_name' => 'Eunice', 'last_name' => 'Gumede', 'designation' => 'radiographer', 'phone' => '+27 72 200 0005', 'email' => 'eunice.gumede@hospital.co.za', 'gender' => 'female'],
            ['first_name' => 'Frank', 'last_name' => 'Chirwa', 'designation' => 'receptionist', 'phone' => '+27 72 200 0006', 'email' => 'frank.chirwa@hospital.co.za', 'gender' => 'male'],
            ['first_name' => 'Gladys', 'last_name' => 'Mabena', 'designation' => 'nurse', 'phone' => '+27 72 200 0007', 'email' => 'gladys.mabena@hospital.co.za', 'gender' => 'female'],
            ['first_name' => 'Henry', 'last_name' => 'Masuku', 'designation' => 'administrator', 'phone' => '+27 72 200 0008', 'email' => 'henry.masuku@hospital.co.za', 'gender' => 'male'],
            ['first_name' => 'Irene', 'last_name' => 'Maposa', 'designation' => 'pharmacist', 'phone' => '+27 72 200 0009', 'email' => 'irene.maposa@hospital.co.za', 'gender' => 'female'],
            ['first_name' => 'Joseph', 'last_name' => 'Nyathi', 'designation' => 'porter', 'phone' => '+27 72 200 0010', 'email' => 'joseph.nyathi@hospital.co.za', 'gender' => 'male'],
        ];

        foreach ($staffData as $i => $s) {
            $dept = $departments[$i % $departments->count()];
            Staff::create(array_merge($s, [
                'employee_number' => 'EMP' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'department_id' => $dept->id,
                'hire_date' => Carbon::now()->subMonths(rand(6, 48))->toDateString(),
                'date_of_birth' => Carbon::now()->subYears(rand(25, 55))->toDateString(),
            ]));
        }

        // --- Visits ---
        $visits = collect();
        $statuses = ['waiting', 'in_progress', 'completed', 'completed', 'completed'];
        $complaints = [
            'Headache and fever for 3 days',
            'Persistent cough and shortness of breath',
            'Lower back pain radiating to legs',
            'Abdominal pain and nausea',
            'Chest pain on exertion',
            'Skin rash on arms and legs',
            'Dizziness and blurred vision',
            'Sore throat and difficulty swallowing',
            'Joint pain and swelling in knees',
            'Follow-up for hypertension management',
            'Routine antenatal checkup',
            'Child vaccination visit',
        ];
        $diagnoses = [
            'Acute upper respiratory tract infection',
            'Community acquired pneumonia',
            'Lumbar disc prolapse',
            'Acute gastritis',
            'Stable angina pectoris',
            'Contact dermatitis',
            'Benign positional vertigo',
            'Acute pharyngitis',
            'Osteoarthritis',
            'Hypertension - controlled',
            'Normal pregnancy 28 weeks',
            'Routine immunisation - up to date',
        ];

        for ($i = 0; $i < 15; $i++) {
            $patient = $patients->random();
            $doctor = $doctors->random();
            $status = $statuses[array_rand($statuses)];
            $visitDate = Carbon::now()->subDays(rand(0, 30));

            $visits->push(Visit::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'department_id' => $doctor->department_id,
                'visit_date' => $visitDate,
                'complaint' => $complaints[$i % count($complaints)],
                'diagnosis' => $status === 'completed' ? $diagnoses[$i % count($diagnoses)] : null,
                'status' => $status,
                'notes' => $status === 'completed' ? 'Patient examined and treated. Follow-up in 2 weeks.' : null,
            ]));
        }

        // --- Admissions ---
        $admissions = collect();
        $reasons = [
            'Severe pneumonia requiring IV antibiotics',
            'Post-operative monitoring after appendectomy',
            'Diabetic ketoacidosis',
            'Observation after head injury',
            'Labour and delivery',
        ];

        // Admit 5 distinct patients to distinct available beds.
        // The Admission model automatically marks each bed as occupied.
        $availableBeds = $beds->filter(fn ($b) => $b->status->value === 'available')->values();
        $admissionPatients = $patients->shuffle()->take(5)->values();

        foreach ($admissionPatients as $i => $patient) {
            $bed = $availableBeds[$i];
            $doctor = $doctors->random();
            $ward = $wards->firstWhere('id', $bed->ward_id);
            $admittedAt = Carbon::now()->subDays(rand(1, 10));

            $admissions->push(Admission::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'department_id' => $ward->department_id,
                'ward_id' => $ward->id,
                'bed_id' => $bed->id,
                'admitted_at' => $admittedAt,
                'reason_for_admission' => $reasons[$i % count($reasons)],
                'status' => 'admitted',
            ]));
        }

        // Add some discharged admissions
        for ($i = 0; $i < 3; $i++) {
            $patient = $patients->random();
            $doctor = $doctors->random();
            $ward = $wards->random();
            $admittedAt = Carbon::now()->subDays(rand(15, 45));

            $admissions->push(Admission::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'department_id' => $ward->department_id,
                'ward_id' => $ward->id,
                'bed_id' => null,
                'admitted_at' => $admittedAt,
                'discharged_at' => $admittedAt->copy()->addDays(rand(3, 10)),
                'reason_for_admission' => $reasons[$i % count($reasons)],
                'diagnosis' => $diagnoses[$i % count($diagnoses)],
                'discharge_notes' => 'Patient recovered well. Discharged with oral medications.',
                'status' => 'discharged',
            ]));
        }

        // --- Prescriptions ---
        $prescriptions = collect();
        $completedVisits = $visits->filter(fn ($v) => $v->status->value === 'completed');

        foreach ($completedVisits->take(8) as $visit) {
            $prescription = Prescription::create([
                'patient_id' => $visit->patient_id,
                'doctor_id' => $visit->doctor_id,
                'visit_id' => $visit->id,
                'prescribed_at' => $visit->visit_date,
                'status' => collect(['pending', 'dispensed', 'dispensed', 'partially_dispensed'])->random(),
                'notes' => 'Take as directed.',
            ]);
            $prescriptions->push($prescription);

            // Add 1-3 items per prescription
            $itemCount = rand(1, 3);
            $selectedMeds = $medications->random($itemCount);
            foreach ($selectedMeds as $med) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_id' => $med->id,
                    'dosage' => collect(['1 tablet 3 times daily', '2 tablets twice daily', '1 capsule daily', '5ml 3 times daily'])->random(),
                    'quantity' => rand(7, 30),
                    'duration_days' => rand(5, 14),
                    'instructions' => collect(['Take after meals', 'Take on empty stomach', 'Take before bed', 'Take with water'])->random(),
                    'dispensed' => $prescription->status === 'dispensed',
                ]);
            }
        }

        // --- Invoices ---
        foreach ($completedVisits->take(10) as $visit) {
            $patient = $patients->firstWhere('id', $visit->patient_id);

            // Invoice number auto-generates; total is derived from items.
            $invoice = Invoice::create([
                'patient_id' => $patient->id,
                'visit_id' => $visit->id,
                'payment_method' => $patient->billing_type->value,
                'paid_amount' => 0,
            ]);

            // Add invoice items — the InvoiceItem model auto-calculates each
            // line total and recalculates the parent invoice total.
            $items = [
                ['description' => 'Consultation fee', 'tariff_code' => '0190', 'unit_price' => rand(250, 500)],
                ['description' => 'Medication', 'tariff_code' => '0300', 'unit_price' => rand(50, 300)],
                ['description' => 'Lab tests', 'tariff_code' => '3600', 'unit_price' => rand(100, 400)],
            ];

            foreach (array_slice($items, 0, rand(1, 3)) as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'tariff_code' => $item['tariff_code'],
                    'quantity' => 1,
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Simulate a payment — the Invoice model derives the status.
            $invoice->refresh();
            $paymentScenario = collect(['unpaid', 'paid', 'paid', 'partial'])->random();
            $paidAmount = match ($paymentScenario) {
                'paid' => (float) $invoice->total_amount,
                'partial' => round((float) $invoice->total_amount * rand(30, 70) / 100, 2),
                default => 0,
            };

            if ($paidAmount > 0) {
                $invoice->update(['paid_amount' => $paidAmount]);
            }
        }
    }
}
