<?php

namespace App\Commands;

use App\Commands\Questionnaires\DateQuestionnaire;
use App\Commands\Questionnaires\FlightEngines\FlightEngineQuestionnaireContract;
use App\Enums\FlightEngine;
use App\Enums\Market;
use App\PeriodBuilder;
use App\Pipelines\FlightEngines\FlightWrapper;
use App\ValueObjects\DatePair;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class FlightLinksCommand extends Command
{
    /**
     * @param array<int, FlightEngineQuestionnaireContract> $flightEngineQuestionnaires
     */
    public function __construct(
        private readonly array $flightEngineQuestionnaires
    ) {
        parent::__construct();
    }

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'generate:flights';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Pipeline $pipeline)
    {
        $flightEngine = FlightEngine::from($this->choice(
            'What website should I generate links for?',
            Collection::make(FlightEngine::cases())->map(fn(FlightEngine $engine) => $engine->value)->toArray(),
            'skyscanner',
        ));

        $dateQuestionnaire = new DateQuestionnaire($this);
        $departureRangeA = $dateQuestionnaire->validDate('Departure date (beginning of range)');
        $departureRangeB = $dateQuestionnaire->validDate('Departure date (end of range)');

        $departurePeriodBuilder = PeriodBuilder::query(CarbonPeriod::between($departureRangeA, $departureRangeB));

        $daysOfWeekToExclude = $this->choice(
            'Which days would you like to exclude?',
            ['None', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            multiple: true,
        );

        $departurePeriodBuilder = $departurePeriodBuilder->withoutDays(...$daysOfWeekToExclude);

        $this->table(['Date', 'Day'], Collection::make($departurePeriodBuilder->get())->map(fn(Carbon $date) => [
            $date->format('Y-m-d'),
            $date->englishDayOfWeek,
        ])->toArray());

        $isRoundTrip = $this->confirm('Is this a round trip?', true);

        if ($isRoundTrip === true) {
            $returnRangeA = $dateQuestionnaire->validDate('Return date (beginning of range)');
            $returnRangeB = $dateQuestionnaire->validDate('Return date (end of range)');

            $returnPeriodBuilder = PeriodBuilder::query(CarbonPeriod::between($returnRangeA, $returnRangeB));

            $daysOfWeekToExclude = $this->choice(
                'Which days would you like to exclude?',
                ['None', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                multiple: true
            );

            $returnPeriodBuilder = $returnPeriodBuilder->withoutDays(...$daysOfWeekToExclude);

            $this->table(['Date', 'Day'], Collection::make($returnPeriodBuilder->get())->map(fn(Carbon $date) => [
                $date->format('Y-m-d'),
                $date->englishDayOfWeek,
            ])->toArray());

            $minimumStay = $dateQuestionnaire->validInterval('What is your minimum stay?');
            $maximumStay = $dateQuestionnaire->validInterval('What is your maximum stay?');

            $dates = Collection::make($departurePeriodBuilder->get())
                ->map(function (Carbon $departure) use ($returnPeriodBuilder, $minimumStay, $maximumStay) {
                    return Collection::make($returnPeriodBuilder->get())
                        ->reject(fn(Carbon $return) => $return->lessThan($departure->copy()->add($minimumStay)))
                        ->reject(fn(Carbon $return) => $return->greaterThan($departure->copy()->add($maximumStay)))
                        ->map(fn(Carbon $return) => new DatePair(new CarbonImmutable($departure), new CarbonImmutable($return)));
                })
                ->flatten(1);
        }

        if ($isRoundTrip === false) {
            $dates = Collection::make($departurePeriodBuilder->get())
                ->map(fn(Carbon $departure) => new CarbonImmutable($departure));
        }

        $flightEngineQuestionnaire = Collection::make($this->flightEngineQuestionnaires)
            ->sole(fn(FlightEngineQuestionnaireContract $questionnaire) => $questionnaire->supports($flightEngine));

        $flightEngineData = ($flightEngineQuestionnaire)($this);

        $counter = 0;

        $default = __DIR__ . '/flights.txt';
        $path = $this->ask("Provide a fully qualified path to a file you'd like to save the links to", $default);

        File::exists($path) && File::delete($path);
        File::put($path, '');

        foreach ($flightEngineData->markets() as $market) {
            foreach ($dates as $date) {
                foreach ($flightEngineData->origins() as $origin) {
                    foreach ($flightEngineData->destinations() as $destination) {
                        $flightWrapper = new FlightWrapper(
                            $flightEngineData,
                            $date,
                            $market,
                            $origin,
                            $destination,
                            new Uri()
                        );

                        $flightWrapper = $pipeline->send($flightWrapper)
                            ->through($flightEngine->pipes())
                            ->thenReturn();

                        $this->info((string) $flightWrapper->carry);
                        File::append($path, (string) $flightWrapper->carry . PHP_EOL);
                        $counter++;
                    }
                }
            }
        }

        $this->info("Generated {$counter} links");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
