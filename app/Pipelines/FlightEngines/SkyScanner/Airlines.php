<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

class Airlines
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next)
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        if ($payload->flightEngineQuestionnaireData->includeAirlines === null) {
            return $next($payload);
        }

        $payload->carry = Uri::withQueryValues($payload->carry, [
            'airlines' => $payload->flightEngineQuestionnaireData->includeAirlines->map(fn(string $airline) => Str::of($airline)
                ->trim()
            )->implode(',')
        ]);

        return $next($payload);
    }
}
