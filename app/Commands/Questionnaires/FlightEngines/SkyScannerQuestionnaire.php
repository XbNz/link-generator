<?php

namespace App\Commands\Questionnaires\FlightEngines;

use App\Commands\Questionnaires\DateQuestionnaire;
use App\DTOs\FlightEngines\FlightEngineQuestionnaireData;
use App\DTOs\FlightEngines\SkyScannerQuestionnaireData;
use App\Enums\Cabin;
use App\Enums\FlightEngine;
use App\Enums\Market;
use App\Pipelines\FlightEngines\SkyScanner\DepartureDate;
use Carbon\CarbonInterval;
use Carbon\Exceptions\InvalidIntervalException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class SkyScannerQuestionnaire implements FlightEngineQuestionnaireContract
{
    public function __invoke(Command $command): SkyScannerQuestionnaireData
    {
        $originAirports = $this->getOriginAirports($command);
        $destinationAirports = $this->getDestinationAirports($command);

        $wantsToIncludeAirlines = $command->confirm('Do you want to include specific airlines?', false);
        $airlines = $wantsToIncludeAirlines ? $this->getAirlines($command) : null;

        $adults = $this->getAdults($command);
        $children = $this->getChildren($command);
        $infants = $this->getInfants($command);

        $maxStopovers = $this->getMaxStopovers($command);
        $maxDuration = (new DateQuestionnaire($command))->validInterval('Maximum duration of entire trip');
        $alternatives = $command->confirm('Include alternative airports', false);
        $cabinClass = Cabin::from($command->choice(
            'Cabin class',
            Collection::make(Cabin::cases())
                ->map(fn (Cabin $cabin) => (string) $cabin->value)
                ->toArray(),
            Cabin::Economy->value,
        ));
        $market = Market::from($command->choice(
            'What markets would you like links for?',
            Collection::make(Market::cases())
                ->map(fn (Market $market) => (string) $market->value)
                ->toArray(),
            Market::Random->value,
        ));

        $wantsCustomMarket = $command->confirm('Would you like to specify custom markets? (e.g. US, UK, DE)', false);
        $customMarkets = $wantsCustomMarket ? $this->getCustomMarkets($command) : [];
        $allMarkets = Collection::make($market->iso2())
            ->merge($customMarkets)
            ->unique();
        $marketLimit = $this->getMarketLimit($command, $allMarkets);

        $currency = $this->getCurrency($command);

        return new SkyScannerQuestionnaireData(
            $originAirports,
            $destinationAirports,
            $airlines,
            $adults,
            $children,
            $infants,
            $cabinClass,
            $maxStopovers,
            $alternatives,
            $maxDuration,
            $allMarkets,
            $currency,
        );
    }

    public function supports(FlightEngine $flightEngine): bool
    {
        return $flightEngine === FlightEngine::SkyScanner;
    }

    /**
     * @return Collection<int, string>
     */
    private function getOriginAirports(Command $command): Collection
    {
        $originAirports = Collection::make(explode(',', $command->ask('What are the origin airports? (e.g. LHR, JFK, DFW)')))
            ->map(fn(string $airport) => Str::of($airport)->trim()->lower()->value())
            ->filter(fn(string $airport) => strlen($airport) === 3);

        while ($originAirports->isEmpty() === true) {
            return $this->getOriginAirports($command);
        }

        return $originAirports;
    }

    /**
     * @return Collection<int, string>
     */
    public function getDestinationAirports(Command $command): Collection
    {
        $destinationAirports = Collection::make(explode(',', $command->ask('What are the destination airports?')))
            ->map(fn(string $airport) => Str::of($airport)->trim()->lower()->value())
            ->filter(fn(string $airport) => strlen($airport) === 3);

        while ($destinationAirports->isEmpty() === true) {
            return $this->getDestinationAirports($command);
        }

        return $destinationAirports;
    }

    /**
     * @return Collection<int, string>
     */
    public function getAirlines(Command $command): Collection
    {
        $airlines = Collection::make(explode(',', $command->ask('Which airlines? (e.g. -32456 = Cathay Pacific)')))
            ->map(fn(string $airline) => Str::of($airline)->trim()->value())
            ->reject(fn(string $airline) => Str::of($airline)->startsWith('-') === false);

        while ($airlines->isEmpty() === true) {
            return $this->getAirlines($command);
        }

        return $airlines;
    }

    // same thing fro adults, children, infants

    private function getAdults(Command $command): int
    {
        $adults = $command->ask('How many adults?', '1');

        while (!is_numeric($adults)) {
            return $this->getAdults($command);
        }

        return (int) $adults;
    }

    private function getChildren(Command $command): int
    {
        $children = $command->ask('How many children?', '0');

        while (!is_numeric($children)) {
            return $this->getChildren($command);
        }

        return (int) $children;
    }

    private function getInfants(Command $command): int
    {
        $infants = $command->ask('How many infants?', '0');

        while (!is_numeric($infants)) {
            return $this->getInfants($command);
        }

        return (int) $infants;
    }

    private function getMaxStopovers(Command $command): int
    {
        $maxStopovers = $command->ask('Maximum stopovers', '0');

        while (!is_numeric($maxStopovers)) {
            return $this->getMaxStopovers($command);
        }

        return (int) $maxStopovers;
    }

    /**
     * @return Collection<int, string>
     */
    private function getCustomMarkets(Command $command): Collection
    {
        $customMarkets = Collection::make(explode(',', $command->ask('Which markets?')))
            ->map(fn(string $market) => Str::of($market)->trim()->value())
            ->filter(fn(string $market) => strlen($market) === 2);

        while ($customMarkets->isEmpty() === true) {
            return $this->getCustomMarkets($command);
        }

        return $customMarkets;
    }

    private function getMarketLimit(Command $command, Collection $allMarkets): int
    {
        $marketLimit = $command->ask('Market limit (higher = more links)', '5');

        while (!is_numeric($marketLimit) || $allMarkets->count() < $marketLimit) {
            return $this->getMarketLimit($command, $allMarkets);
        }

        return (int) $marketLimit;
    }

    private function getCurrency(Command $command): string
    {
        $currency = Str::of($command->ask('Currency', 'USD'))->trim();

        while ($currency->length() !== 3) {
            return $this->getCurrency($command);
        }

        return $currency->value();
    }
}
