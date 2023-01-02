<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use League\Pipeline\StageInterface;
use Webmozart\Assert\Assert;

class Origin
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next)
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        $payload->carry = $payload->carry->withPath($payload->carry->getPath() . '/' . $payload->origin);

        return $next($payload);
    }
}
