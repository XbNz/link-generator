<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\DTOs\FlightEngines\SkyScannerQuestionnaireData;
use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use GuzzleHttp\Psr7\Uri;
use Webmozart\Assert\Assert;

class Duration
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next): mixed
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);
        Assert::isInstanceOf($payload->flightEngineQuestionnaireData, SkyScannerQuestionnaireData::class);

        $payload->carry = Uri::withQueryValues($payload->carry, [
            'duration' => (string) $payload->flightEngineQuestionnaireData->maxDuration->totalMinutes
        ]);

        return $next($payload);
    }
}
