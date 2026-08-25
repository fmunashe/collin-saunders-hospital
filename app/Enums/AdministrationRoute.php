<?php

namespace App\Enums;

enum AdministrationRoute: string
{
    case Oral = 'oral';
    case Intravenous = 'iv';
    case Intramuscular = 'im';
    case Subcutaneous = 'sc';
    case Topical = 'topical';
    case Inhalation = 'inhalation';
    case Rectal = 'rectal';
    case Sublingual = 'sublingual';
}
