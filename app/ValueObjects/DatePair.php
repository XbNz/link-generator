<?php

namespace App\ValueObjects;

use Carbon\CarbonImmutable;

class DatePair
{
    public function __construct(
        public readonly CarbonImmutable $dateA,
        public readonly CarbonImmutable $dateB
    ) {
    }
}
