<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\DTOs\FlightEngines\SkyScannerQuestionnaireData;
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
    public function __invoke($payload, Closure $next): mixed
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);
        Assert::isInstanceOf($payload->flightEngineQuestionnaireData, SkyScannerQuestionnaireData::class);

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
