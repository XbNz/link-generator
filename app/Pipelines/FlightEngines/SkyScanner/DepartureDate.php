<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use App\ValueObjects\DatePair;
use Carbon\CarbonImmutable;
use Closure;
use GuzzleHttp\Psr7\Uri;
use Webmozart\Assert\Assert;

class DepartureDate
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next)
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        if ($payload->dates instanceof CarbonImmutable) {
            $payload->carry = $payload->carry->withPath($payload->carry->getPath() . '/' . $payload->dates->format('ymd'));
            return $next($payload);
        }

        $payload->carry = $payload->carry->withPath($payload->carry->getPath() . '/' . $payload->dates->dateA->format('ymd'));
        return $next($payload);
    }
}
