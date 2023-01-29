<?php

namespace Tests\Unit;

use App\PeriodBuilder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Generator;
use PHPUnit\Framework\TestCase;

class PeriodBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider dayProvider
     **/
    public function applying_a_day_exclusion_works(string $dayName): void
    {
        // Arrange
        $dayName = strtolower($dayName);
        $builder = new PeriodBuilder(CarbonPeriod::between('2021-01-01', '2021-01-31'));

        // Act
        $period = $builder->withoutDays($dayName);

        // Assert
        $days = iterator_to_array($period->get()->map(fn(Carbon $date) => strtolower($date->dayName)));
        $this->assertNotContains($dayName, $days);
        $this->assertLessThan(39, count($days));
    }

    /**
     * @test
     * @dataProvider dayProvider
     **/
    public function with_days_works(string $dayName): void
    {
        // Arrange
        $dayName = strtolower($dayName);
        $builder = new PeriodBuilder(CarbonPeriod::between('2021-01-01', '2021-01-31'));

        // Act
        $period = $builder->withDays($dayName);

        // Assert
        $days = iterator_to_array($period->get()->map(fn(Carbon $date) => strtolower($date->dayName)));
        $this->assertContains($dayName, $days);
        $this->assertLessThan(6, count($days));
    }

    public function dayProvider(): Generator
    {
        yield from [
            'Monday' => ['Monday'],
            'Tuesday' => ['Tuesday'],
            'Wednesday' => ['Wednesday'],
            'Thursday' => ['Thursday'],
            'Friday' => ['Friday'],
            'Saturday' => ['Saturday'],
            'Sunday' => ['Sunday'],
        ];
    }
}
