<?php

namespace App\Enums;

use App\Momondo;
use App\SkyScanner;
use League\Pipeline\Pipeline;
use League\Pipeline\PipelineBuilder;

enum FlightEngine: string
{
    case SkyScanner = 'skyscanner';
    case Momondo = 'momondo';

    public function friendlyName(): string
    {
        return match ($this) {
            self::SkyScanner => 'SkyScanner',
            self::Momondo => 'Momondo',
        };
    }

    public function pipes(): Pipeline
    {
        return match ($this) {
            self::SkyScanner => (new PipelineBuilder())
                ->add(new SkyScanner\DepartureDatePipe())
                ->add(new SkyScanner\DepartureAirportPipe())
                ->add(new SkyScanner\DestinationAirportPipe())
                ->add(new SkyScanner\DestinationDatePipe())
                ->build(),
            self::Momondo => (new PipelineBuilder())
                ->add(new Momondo\DepartureDatePipe())
                ->add(new Momondo\DepartureAirportPipe())
                ->add(new Momondo\DestinationAirportPipe())
                ->add(new Momondo\DestinationDatePipe())
                ->build(),
        };
    }
}
