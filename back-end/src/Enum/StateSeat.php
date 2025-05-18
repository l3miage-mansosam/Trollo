<?php

namespace App\Enum;

enum StateSeat: string
{
    case AVAILABLE = 'available';
    case BOOKED = 'booked';
    case RESERVED = 'reserved';
}
