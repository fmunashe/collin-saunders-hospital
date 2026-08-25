<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Received = 'received';
    case Dispensed = 'dispensed';
    case Adjustment = 'adjustment';
    case Returned = 'returned';
    case Expired = 'expired';
}
