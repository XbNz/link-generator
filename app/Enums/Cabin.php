<?php

namespace App\Enums;

enum Cabin: string
{
    case Economy = 'economy';
    case PremiumEconomy = 'premium_economy';
    case Business = 'business';
    case First = 'first';
}
