<?php

namespace App\DTOs\FlightEngines;

use Illuminate\Support\Collection;

interface FlightEngineQuestionnaireData
{
    /**
     * @return Collection<int, string>
     */
    public function markets(): Collection;

    /**
     * @return Collection<int, string>
     */
    public function origins(): Collection;

    /**
     * @return Collection<int, string>
     */
    public function destinations(): Collection;
}
