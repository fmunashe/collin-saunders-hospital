<?php

namespace App\Enums;

enum ReferralPriority: string
{
    case Routine = 'routine';
    case Urgent = 'urgent';
    case Emergency = 'emergency';
}
