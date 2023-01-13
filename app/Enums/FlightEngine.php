<?php

namespace App\Enums;

use App\Pipelines\FlightEngines\SkyScanner\Airlines;
use App\Pipelines\FlightEngines\SkyScanner\Alternatives;
use App\Pipelines\FlightEngines\SkyScanner\CabinClass;
use App\Pipelines\FlightEngines\SkyScanner\ConfigureUriStructure;
use App\Pipelines\FlightEngines\SkyScanner\DepartureDate;
use App\Pipelines\FlightEngines\SkyScanner\Destination;
use App\Pipelines\FlightEngines\SkyScanner\Duration;
use App\Pipelines\FlightEngines\SkyScanner\Localization;
use App\Pipelines\FlightEngines\SkyScanner\Origin;
use App\Pipelines\FlightEngines\SkyScanner\Passengers;
use App\Pipelines\FlightEngines\SkyScanner\ReturnDate;
use App\Pipelines\FlightEngines\SkyScanner\Stopovers;
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

    /**
     * @return array<int, class-string>
     */
    public function pipes(): array
    {
        return match ($this) {
            self::SkyScanner => [
                ConfigureUriStructure::class,
                Origin::class,
                Destination::class,
                DepartureDate::class,
                ReturnDate::class,
                Passengers::class,
                Stopovers::class,
                CabinClass::class,
                Duration::class,
                Airlines::class,
                Alternatives::class,
                Localization::class
            ],
            self::Momondo => []
        };
    }
}
