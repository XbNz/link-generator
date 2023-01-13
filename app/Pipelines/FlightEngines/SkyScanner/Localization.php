<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\DTOs\FlightEngines\SkyScannerQuestionnaireData;
use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use GuzzleHttp\Psr7\Uri;
use Webmozart\Assert\Assert;

class Localization
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next): mixed
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);
        Assert::isInstanceOf($payload->flightEngineQuestionnaireData, SkyScannerQuestionnaireData::class);

        $payload->carry = Uri::withQueryValues($payload->carry, [
            'market' => $payload->market,
            'currency' => $payload->flightEngineQuestionnaireData->currency,
            'locale' => config('app.locale'),
        ]);

        return $next($payload);
    }
}
