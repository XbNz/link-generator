<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use App\ValueObjects\DatePair;
use Carbon\CarbonImmutable;
use Closure;
use League\Pipeline\StageInterface;
use Webmozart\Assert\Assert;

class Destination
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next): mixed
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        $payload->carry = $payload->carry->withPath($payload->carry->getPath() . '/' . $payload->destination);

        return $next($payload);
    }
}
