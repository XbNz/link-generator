<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use Carbon\CarbonImmutable;
use Closure;
use Webmozart\Assert\Assert;

class ReturnDate
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next): mixed
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        if ($payload->dates instanceof CarbonImmutable) {
            return $next($payload);
        }

        $payload->carry = $payload->carry->withPath($payload->carry->getPath() . '/' . $payload->dates->dateB->format('ymd'));
        return $next($payload);
    }
}
