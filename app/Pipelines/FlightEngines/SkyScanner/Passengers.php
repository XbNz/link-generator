<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\DTOs\FlightEngines\SkyScannerQuestionnaireData;
use App\Pipelines\FlightEngines\FlightWrapper;
use App\ValueObjects\DatePair;
use Closure;
use GuzzleHttp\Psr7\Uri;
use Webmozart\Assert\Assert;

class Passengers
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next): mixed
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);
        Assert::isInstanceOf($payload->flightEngineQuestionnaireData, SkyScannerQuestionnaireData::class);

        $queries = [];

        if ($payload->flightEngineQuestionnaireData->adults > 0) {
            $queries['adults'] = (string) $payload->flightEngineQuestionnaireData->adults;
            $queries['adultsv2'] = (string) $payload->flightEngineQuestionnaireData->adults;
        }

        if ($payload->flightEngineQuestionnaireData->children > 0) {
            $queries['children'] = (string) $payload->flightEngineQuestionnaireData->children;
            $queries['childrenv2'] = (string) $payload->flightEngineQuestionnaireData->children;
        }

        if ($payload->flightEngineQuestionnaireData->infants > 0) {
            $queries['infants'] = (string) $payload->flightEngineQuestionnaireData->infants;
        }

        $payload->carry = Uri::withQueryValues($payload->carry, $queries);

        return $next($payload);
    }
}
