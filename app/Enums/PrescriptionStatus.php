<?php

namespace App\Enums;

enum PrescriptionStatus: string
{
    case Pending = 'pending';
    case Dispensed = 'dispensed';
    case PartiallyDispensed = 'partially_dispensed';
    case Cancelled = 'cancelled';
}
