<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use GuzzleHttp\Psr7\Uri;
use Webmozart\Assert\Assert;

class Alternatives
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next)
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        if ($payload->flightEngineQuestionnaireData->alternatives === false) {
            return $next($payload);
        }

        $payload->carry = Uri::withQueryValues($payload->carry, [
            'inboundaltsenabled' => 'true',
            'outboundaltsenabled' => 'true'
        ]);

        return $next($payload);
    }
}
