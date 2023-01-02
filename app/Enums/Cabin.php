<?php

namespace App\Enums;

enum Cabin: string
{
    case Economy = 'economy';
    case PremiumEconomy = 'premium_economy';
    case Business = 'business';
    case First = 'first';

    public function skyScannerName(): string
    {
        return match ($this) {
            self::Economy => 'economy',
            self::PremiumEconomy => 'premiumeconomy',
            self::Business => 'business',
            self::First => 'first',
        };
    }
}
