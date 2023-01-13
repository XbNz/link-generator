<?php

namespace App\Pipelines\FlightEngines\SkyScanner;

use App\Pipelines\FlightEngines\FlightWrapper;
use Closure;
use League\Pipeline\StageInterface;
use Webmozart\Assert\Assert;

class ConfigureUriStructure
{
    /**
     * @param FlightWrapper $payload
     */
    public function __invoke($payload, Closure $next): mixed
    {
        Assert::isInstanceOf($payload, FlightWrapper::class);

        $payload->carry = $payload->carry->withScheme('https')
            ->withHost('skyscanner.com')
            ->withPath($payload->carry->getPath() . '/transport/flights');

        return $next($payload);
    }
}
