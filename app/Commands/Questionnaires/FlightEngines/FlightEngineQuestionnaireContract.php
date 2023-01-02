<?php

namespace App\Commands\Questionnaires\FlightEngines;

use App\DTOs\FlightEngines\FlightEngineQuestionnaireData;
use App\Enums\FlightEngine;
use LaravelZero\Framework\Commands\Command;

interface FlightEngineQuestionnaireContract
{
    public function __invoke(Command $command): FlightEngineQuestionnaireData;
    public function supports(FlightEngine $flightEngine): bool;
}
