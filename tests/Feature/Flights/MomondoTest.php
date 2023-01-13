<?php

namespace Tests\Feature\Flights;

use App\Enums\Cabin;
use App\Enums\Market;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MomondoTest extends TestCase
{
    /** @test **/
    public function happy_path_skyscanner(): void
    {
        $file = tmpfile();

        $this->artisan('generate:flights')
            ->expectsChoice('What website should I generate links for?', 'skyscanner', ['skyscanner', 'momondo'])
            ->expectsQuestion('Departure date (beginning of range)', 'Jan 1, 2023')
            ->expectsQuestion('Departure date (end of range)', 'Jan 2, 2023')
            ->expectsChoice(
                'Which days would you like to exclude?',
                ['Sunday'],
                ['None', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            )
            ->expectsConfirmation('Is this a round trip?', 'yes')
            ->expectsQuestion('Return date (beginning of range)', 'Jan 5, 2023')
            ->expectsQuestion('Return date (end of range)', 'Jan 12, 2023')
            ->expectsChoice(
                'Which days would you like to exclude?',
                ['Thursday'],
                ['None', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            )
            ->expectsQuestion('What is your minimum stay?', '9 days')
            ->expectsQuestion('What is your maximum stay?', '10 days')

            // Engine-specific questions

            ->expectsQuestion('What are the origin airports? (e.g. LHR, JFK, DFW)', 'FRA, AMS')
            ->expectsQuestion('What are the destination airports?', 'LAX, SFO')
            ->expectsConfirmation('Do you want to include specific airlines?', 'yes')
            ->expectsQuestion('Which airlines? (e.g. -32456 = Cathay Pacific)', '-32456, -12345')
            ->expectsQuestion('How many adults?', '1')
            ->expectsQuestion('How many children?', '1')
            ->expectsQuestion('How many infants?', '1')
            ->expectsQuestion('Maximum stopovers', '1')
            ->expectsQuestion('Maximum duration of entire trip', '25 hours')
            ->expectsConfirmation('Include alternative airports', 'yes')
            ->expectsChoice('Cabin class', 'economy', Collection::make(Cabin::cases())
                ->map(fn(Cabin $cabin) => $cabin->value)->toArray())
            ->expectsChoice('What markets would you like links for?', 'scandinavia', Collection::make(Market::cases())
                ->map(fn(Market $market) => $market->value)->toArray())
            ->expectsQuestion('Market limit (higher = more links)', '5')
            ->expectsConfirmation('Would you like to specify custom markets? (e.g. US, UK, DE)', 'yes')
            ->expectsQuestion('Which markets?', 'US, DE, UK')
            ->expectsQuestion('Currency', 'CAD')
            ->expectsQuestion("Provide a fully qualified path to a file you'd like to save the links to", stream_get_meta_data($file)['uri'])
            ->expectsOutputToContain('Generated 32 links')
            ->expectsOutputToContain('fra/lax')
            ->expectsOutputToContain('fra/sfo')
            ->expectsOutputToContain('ams/lax')
            ->expectsOutputToContain('ams/sfo');

        $contentCollection = File::lines(stream_get_meta_data($file)['uri']);

        $this->assertEquals(33, $contentCollection->count());
    }

    /** @test **/
    public function happy_path_skyscanner_one_way(): void
    {
        $file = tmpfile();

        $this->artisan('generate:flights')
            ->expectsChoice('What website should I generate links for?', 'skyscanner', ['skyscanner', 'momondo'])
            ->expectsQuestion('Departure date (beginning of range)', 'Jan 1, 2023')
            ->expectsQuestion('Departure date (end of range)', 'Jan 2, 2023')
            ->expectsChoice(
                'Which days would you like to exclude?',
                ['Sunday'],
                ['None', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            )
            ->expectsConfirmation('Is this a round trip?', 'no')

            // Engine-specific questions

            ->expectsQuestion('What are the origin airports? (e.g. LHR, JFK, DFW)', 'FRA, AMS')
            ->expectsQuestion('What are the destination airports?', 'LAX, SFO')
            ->expectsConfirmation('Do you want to include specific airlines?', 'no')
            ->expectsQuestion('How many adults?', '1')
            ->expectsQuestion('How many children?', '1')
            ->expectsQuestion('How many infants?', '1')
            ->expectsQuestion('Maximum stopovers', '1')
            ->expectsQuestion('Maximum duration of entire trip', '25 hours')
            ->expectsConfirmation('Include alternative airports', 'yes')
            ->expectsChoice('Cabin class', 'economy', Collection::make(Cabin::cases())
                ->map(fn(Cabin $cabin) => $cabin->value)->toArray())
            ->expectsChoice('What markets would you like links for?', 'scandinavia', Collection::make(Market::cases())
                ->map(fn(Market $market) => $market->value)->toArray())
            ->expectsQuestion('Market limit (higher = more links)', '5')
            ->expectsConfirmation('Would you like to specify custom markets? (e.g. US, UK, DE)', 'yes')
            ->expectsQuestion('Which markets?', 'US, DE, UK')
            ->expectsQuestion('Currency', 'CAD')
            ->expectsQuestion("Provide a fully qualified path to a file you'd like to save the links to", stream_get_meta_data($file)['uri'])
            ->expectsOutputToContain('Generated 32 links')
            ->expectsOutputToContain('fra/lax')
            ->expectsOutputToContain('fra/sfo')
            ->expectsOutputToContain('ams/lax')
            ->expectsOutputToContain('ams/sfo');

        $contentCollection = File::lines(stream_get_meta_data($file)['uri']);

        $this->assertEquals(33, $contentCollection->count());
    }

}
