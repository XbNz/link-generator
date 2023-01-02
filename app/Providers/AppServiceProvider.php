<?php

namespace App\Providers;

use App\Commands\FlightLinksCommand;
use App\Commands\Questionnaires\FlightEngines\SkyScannerQuestionnaire;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->tag([
            SkyScannerQuestionnaire::class,
        ], 'flight-engine-questionnaires');

        $this->app->when(FlightLinksCommand::class)
            ->needs('$flightEngineQuestionnaires')
            ->giveTagged('flight-engine-questionnaires');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
