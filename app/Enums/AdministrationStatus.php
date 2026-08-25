<?php

namespace App\Enums;

enum AdministrationStatus: string
{
    case Administered = 'administered';
    case Missed = 'missed';
    case Refused = 'refused';
    case Held = 'held';
}
