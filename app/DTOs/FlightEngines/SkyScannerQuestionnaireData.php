<?php

namespace App\DTOs\FlightEngines;

use App\Enums\Cabin;
use App\ValueObjects\DatePair;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SkyScannerQuestionnaireData implements FlightEngineQuestionnaireData
{
    /**
     * @param Collection<int, string> $originAirportCodes
     * @param Collection<int, string> $destinationAirportCodes
     * @param ?Collection<int, string> $excludeAirlines
     * @param ?Collection<int, string> $includeAirlines
     * @param Collection<int, string> $markets
     */
    public function __construct(
        public readonly Collection $originAirportCodes,
        public readonly Collection $destinationAirportCodes,
        public readonly ?Collection $excludeAirlines,
        public readonly ?Collection $includeAirlines,
        public readonly int $adults,
        public readonly int $children,
        public readonly int $infants,
        public readonly Cabin $cabin,
        public readonly int $maxStops,
        public readonly CarbonInterval $maxDuration,
        public readonly Collection $markets,
        public readonly string $currency,
    ) {
        Assert::greaterThanEq($adults, 0);
        Assert::greaterThanEq($children, 0);
        Assert::greaterThanEq($infants, 0);
        Assert::greaterThanEq($maxStops, 0);

    }
}
