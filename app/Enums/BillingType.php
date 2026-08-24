<?php

namespace App\Enums;

enum BillingType: string
{
    case Cash = 'cash';
    case MedicalAid = 'medical_aid';
}
