<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case SubmittedToMedicalAid = 'submitted_to_medical_aid';
    case Rejected = 'rejected';
}
