<?php

namespace App\Commands\Questionnaires;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\InvalidIntervalException;
use LaravelZero\Framework\Commands\Command;

class DateQuestionnaire
{
    public function __construct(private Command $command)
    {
    }

    public function validDate(string $query): CarbonImmutable
    {
        $answer = $this->command->ask($query);

        try {
            $date = CarbonImmutable::parse($answer);
        } catch (InvalidFormatException) {
            $this->command->error('Invalid date format. Please try again.');
            return $this->validDate($query);
        }

        while (!$date->isValid()) {
            $this->command->error('Invalid date');
            $this->validDate($query);
        }

        return $date;
    }

    public function validInterval(string $query): CarbonInterval
    {
        $answer = $this->command->ask($query);

        try {
            $interval = CarbonInterval::fromString($answer);
        } catch (InvalidintervalException) {
            $this->command->error('Invalid interval format. Please try again.');
            return $this->validInterval($query);
        }

        return $interval;
    }
}
