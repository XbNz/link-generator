<?php

namespace App\Commands\Questionnaires\FlightEngines;

use App\DTOs\FlightEngines\FlightEngineQuestionnaireData;
use App\DTOs\FlightEngines\SkyScannerQuestionnaireData;
use App\Enums\Cabin;
use App\Enums\FlightEngine;
use App\Enums\Market;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class SkyScannerQuestionnaire implements FlightEngineQuestionnaireContract
{
    public function __invoke(Command $command): SkyScannerQuestionnaireData
    {
        $originAirports = explode(',', $command->ask('What are the origin airports? (e.g. LHR, JFK, DFW)'));
        $destinationAirports = explode(',', $command->ask('What are the destination airports?'));
        $wantsToIncludeAirlines = $command->confirm('Do you want to include specific airlines?', false);
        $airlines = $wantsToIncludeAirlines ? explode(',', $command->ask('Which airlines? (e.g. -32456 = Cathay Pacific)')) : null;
        $adults = $command->ask('How many adults?', 1);
        $children = $command->ask('How many children?', 0);
        $infants = $command->ask('How many infants?', 0);
        $maxStopovers = $command->ask('Maximum stopovers', 0);
        $maxDuration = $command->ask('Maximum duration', '24 hours');
        $alternatives = $command->confirm('Include alternative airports', false);
        $cabinClass = $command->choice(
            'Cabin class',
            Collection::make(Cabin::cases())
                ->map(fn (Cabin $cabin) => (string) $cabin->value)
                ->toArray()
        );
        $market = $command->choice(
            'What markets would you like links for?',
            Collection::make(Market::cases())
                ->map(fn (Market $market) => (string) $market->value)
                ->toArray()
        );
        $wantsCustomMarket = $command->confirm('Would you like to specify custom markets? (e.g. US, UK, DE)', false);
        $customMarkets = $wantsCustomMarket ? explode(',', $command->ask('Which markets?')) : [];
        $marketLimit = $command->ask('Market limit (higher = more links)', 5);
        $currency = $command->ask('Currency', 'USD');

        $allMarkets = Collection::make(Market::from($market)->iso2())
            ->merge($customMarkets)
            ->unique();

        // TODO: Add graceful handling for invalid data. Then complete phpunit tests.

        return new SkyScannerQuestionnaireData(
            Collection::make($originAirports),
            Collection::make($destinationAirports),
            $airlines ? Collection::make($airlines) : null,
            $adults,
            $children,
            $infants,
            Cabin::from($cabinClass),
            $maxStopovers,
            $alternatives,
            CarbonInterval::fromString($maxDuration),
            $allMarkets,
            $currency,
        );
    }

    public function supports(FlightEngine $flightEngine): bool
    {
        return $flightEngine === FlightEngine::SkyScanner;
    }
}
