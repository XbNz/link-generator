<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

class Stopovers
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next)
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        if ($payload->flightEngineQuestionnaireData->maxStops >= 2) {
            return $next($payload);
        }

        $stops = match ($payload->flightEngineQuestionnaireData->maxStops) {
            0 => '!oneStop,!twoPlusStops',
            1 => '!twoPlusStops',
            default => throw new InvalidArgumentException('Invalid number of stops'),
        };

        $payload->carry = Uri::withQueryValues($payload->carry, [
            'stops' => $stops,
        ]);

        return $next($payload);
    }
}
