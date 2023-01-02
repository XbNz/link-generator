<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use GuzzleHttp\Psr7\Uri;
use Webmozart\Assert\Assert;

class CabinClass
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next)
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        $payload->carry = Uri::withQueryValues($payload->carry, [
            'cabinclass' => $payload->flightEngineQuestionnaireData->cabin->skyScannerName(),
        ]);

        return $next($payload);
    }
}