<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

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
    public function __invoke($payload, Closure $next)
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        $queries = [];

        if ($payload->flightEngineQuestionnaireData->adults > 0) {
            $queries['adults'] = $payload->flightEngineQuestionnaireData->adults;
            $queries['adultsv2'] = $payload->flightEngineQuestionnaireData->adults;
        }

        if ($payload->flightEngineQuestionnaireData->children > 0) {
            $queries['children'] = $payload->flightEngineQuestionnaireData->children;
            $queries['childrenv2'] = $payload->flightEngineQuestionnaireData->children;
        }

        if ($payload->flightEngineQuestionnaireData->infants > 0) {
            $queries['infants'] = $payload->flightEngineQuestionnaireData->infants;
        }

        $payload->carry = Uri::withQueryValues($payload->carry, $queries);

        return $next($payload);
    }
}
