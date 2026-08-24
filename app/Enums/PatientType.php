<?php

namespace App\Enums;

enum PatientType: string
{
    case Staff = 'staff';
    case NonStaff = 'non_staff';
}
