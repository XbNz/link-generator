<?php

namespace App\Pipelines\FlightEngines;

use App\Commands\Questionnaires\FlightEngines\FlightEngineQuestionnaireContract;
use App\DTOs\FlightEngines\FlightEngineQuestionnaireData;
use App\ValueObjects\DatePair;
use Carbon\CarbonImmutable;
use Psr\Http\Message\UriInterface;

class FlightWrapper
{
    public function __construct(
        public FlightEngineQuestionnaireData $flightEngineQuestionnaireData,
        public DatePair|CarbonImmutable $dates,
        public string $market,
        public string $origin,
        public string $destination,
        public UriInterface $carry
    ) {
    }
}
