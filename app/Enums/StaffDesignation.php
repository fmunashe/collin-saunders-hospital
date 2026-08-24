<?php

namespace App\Enums;

enum StaffDesignation: string
{
    case Nurse = 'nurse';
    case LabTechnician = 'lab_technician';
    case Radiographer = 'radiographer';
    case Pharmacist = 'pharmacist';
    case Receptionist = 'receptionist';
    case SupportStaff = 'support_staff';
    case Administrator = 'administrator';
    case Technician = 'technician';
    case Cleaner = 'cleaner';
    case Porter = 'porter';
    case SecurityGuard = 'security_guard';

    public function label(): string
    {
        return match ($this) {
            self::Nurse => 'Nurse',
            self::LabTechnician => 'Lab Technician',
            self::Radiographer => 'Radiographer',
            self::Pharmacist => 'Pharmacist',
            self::Receptionist => 'Receptionist',
            self::SupportStaff => 'Support Staff',
            self::Administrator => 'Administrator',
            self::Technician => 'Technician',
            self::Cleaner => 'Cleaner',
            self::Porter => 'Porter',
            self::SecurityGuard => 'Security Guard',
        };
    }
}
