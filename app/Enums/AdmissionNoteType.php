<?php

namespace App\Enums;

enum AdmissionNoteType: string
{
    case Doctor = 'doctor';
    case Nurse = 'nurse';
    case Observation = 'observation';
    case Procedure = 'procedure';
    case General = 'general';
}
