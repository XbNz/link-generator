<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PeriodBuilder
{
    public function __construct(private readonly CarbonPeriod $period)
    {
    }

    public static function query(CarbonPeriod $period): self
    {
        return new self($period);
    }

    public function withoutDays(string ...$days): self
    {
        $days = array_map(fn(string $day) => strtolower($day), $days);
        $newPeriod = $this->period->filter(fn(Carbon $date) => !in_array(strtolower($date->dayName), $days));

        return new self($newPeriod);
    }

    public function withDays(string ...$days): self
    {
        $days = array_map(fn(string $day) => strtolower($day), $days);
        $newPeriod = $this->period->filter(fn(Carbon $date) => in_array(strtolower($date->dayName), $days));

        return new self($newPeriod);
    }

    public function get(): CarbonPeriod
    {
        return $this->period;
    }
}
