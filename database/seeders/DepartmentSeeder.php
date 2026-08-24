<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'General Practice', 'code' => 'GP', 'description' => 'General outpatient consultations'],
            ['name' => 'Emergency', 'code' => 'ER', 'description' => 'Emergency and trauma care'],
            ['name' => 'Internal Medicine', 'code' => 'IM', 'description' => 'Internal medicine and chronic care'],
            ['name' => 'Surgery', 'code' => 'SUR', 'description' => 'Surgical procedures'],
            ['name' => 'Orthopaedics', 'code' => 'ORT', 'description' => 'Bone and joint care'],
            ['name' => 'Paediatrics', 'code' => 'PED', 'description' => 'Children healthcare'],
            ['name' => 'Obstetrics & Gynaecology', 'code' => 'OBG', 'description' => 'Maternity and women health'],
            ['name' => 'Pharmacy', 'code' => 'PHR', 'description' => 'Dispensary and medication management'],
            ['name' => 'Radiology', 'code' => 'RAD', 'description' => 'Imaging and diagnostics'],
            ['name' => 'Laboratory', 'code' => 'LAB', 'description' => 'Pathology and lab services'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
