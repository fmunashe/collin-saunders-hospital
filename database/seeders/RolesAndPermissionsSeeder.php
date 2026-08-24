<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define resources and their permission actions
        $resources = [
            'department',
            'medical-aid-scheme',
            'patient',
            'medical-aid-detail',
            'doctor',
            'staff',
            'ward',
            'bed',
            'visit',
            'admission',
            'medication',
            'prescription',
            'prescription-item',
            'invoice',
            'invoice-item',
            'user',
        ];

        $actions = ['access', 'show', 'create', 'update', 'delete', 'restore'];

        // Create permissions for each resource
        $allPermissions = [];
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissionName = "{$resource}-{$action}";
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
                $allPermissions[] = $permissionName;
            }
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $doctor = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $nurse = Role::firstOrCreate(['name' => 'nurse', 'guard_name' => 'web']);
        $pharmacist = Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $billing = Role::firstOrCreate(['name' => 'billing', 'guard_name' => 'web']);
        $labTechnician = Role::firstOrCreate(['name' => 'lab_technician', 'guard_name' => 'web']);
        $radiographer = Role::firstOrCreate(['name' => 'radiographer', 'guard_name' => 'web']);
        $supportStaff = Role::firstOrCreate(['name' => 'support_staff', 'guard_name' => 'web']);

        // Admin gets all permissions
        $admin->syncPermissions($allPermissions);

        // Doctor permissions
        $doctor->syncPermissions([
            'patient-access', 'patient-show', 'patient-create', 'patient-update',
            'visit-access', 'visit-show', 'visit-create', 'visit-update',
            'admission-access', 'admission-show', 'admission-create', 'admission-update',
            'prescription-access', 'prescription-show', 'prescription-create', 'prescription-update',
            'prescription-item-access', 'prescription-item-show', 'prescription-item-create', 'prescription-item-update',
            'medication-access', 'medication-show',
            'department-access', 'department-show',
            'ward-access', 'ward-show',
            'bed-access', 'bed-show',
            'staff-access', 'staff-show',
        ]);

        // Nurse permissions
        $nurse->syncPermissions([
            'patient-access', 'patient-show', 'patient-update',
            'visit-access', 'visit-show', 'visit-update',
            'admission-access', 'admission-show', 'admission-update',
            'prescription-access', 'prescription-show',
            'ward-access', 'ward-show',
            'bed-access', 'bed-show', 'bed-update',
            'department-access', 'department-show',
            'staff-access', 'staff-show',
        ]);

        // Pharmacist permissions
        $pharmacist->syncPermissions([
            'medication-access', 'medication-show', 'medication-create', 'medication-update',
            'prescription-access', 'prescription-show', 'prescription-update',
            'prescription-item-access', 'prescription-item-show', 'prescription-item-update',
            'patient-access', 'patient-show',
        ]);

        // Receptionist permissions
        $receptionist->syncPermissions([
            'patient-access', 'patient-show', 'patient-create', 'patient-update',
            'medical-aid-detail-access', 'medical-aid-detail-show', 'medical-aid-detail-create', 'medical-aid-detail-update',
            'medical-aid-scheme-access', 'medical-aid-scheme-show',
            'visit-access', 'visit-show', 'visit-create',
            'admission-access', 'admission-show',
            'department-access', 'department-show',
            'doctor-access', 'doctor-show',
            'staff-access', 'staff-show',
        ]);

        // Billing permissions
        $billing->syncPermissions([
            'invoice-access', 'invoice-show', 'invoice-create', 'invoice-update',
            'invoice-item-access', 'invoice-item-show', 'invoice-item-create', 'invoice-item-update',
            'patient-access', 'patient-show',
            'visit-access', 'visit-show',
            'admission-access', 'admission-show',
            'medical-aid-scheme-access', 'medical-aid-scheme-show',
            'medical-aid-detail-access', 'medical-aid-detail-show',
        ]);

        // Lab Technician permissions
        $labTechnician->syncPermissions([
            'patient-access', 'patient-show',
            'visit-access', 'visit-show',
            'admission-access', 'admission-show',
            'department-access', 'department-show',
        ]);

        // Radiographer permissions
        $radiographer->syncPermissions([
            'patient-access', 'patient-show',
            'visit-access', 'visit-show',
            'admission-access', 'admission-show',
            'department-access', 'department-show',
        ]);

        // Support Staff permissions (minimal - view only)
        $supportStaff->syncPermissions([
            'department-access', 'department-show',
            'ward-access', 'ward-show',
            'bed-access', 'bed-show',
            'staff-access', 'staff-show',
        ]);
    }
}
