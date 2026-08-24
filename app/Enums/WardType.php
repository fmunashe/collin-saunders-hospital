<?php

namespace App\Enums;

enum WardType: string
{
    case General = 'general';
    case ICU = 'icu';
    case Maternity = 'maternity';
    case Paediatric = 'paediatric';
}
