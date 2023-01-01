<?php

namespace Tests\Feature;

use Tests\TestCase;

class FlightsCommandTest extends TestCase
{
    /** @test **/
    public function happy_path(): void
    {
        $this->artisan('generate:flights')
            ->expectsChoice('What website should I generate links for?', 'Skyscanner', ['Skyscanner', 'Momondo'])
            ->expectsQuestion('What are the origin airports? (e.g. LHR, JFK, DFW)', 'LHR')
            ->expectsQuestion('What are the destination airports?', 'LAX, YYZ')
            ->expectsQuestion('Departure date (beginning of range)', 'Jan 4')
            ->expectsQuestion('Departure date (end of range)', 'Jan 11')
            ->expectsConfirmation('Would you like to exclude any days of the week from your range?', 'yes')
            ->expectsChoice(
                'Which days would you like to exclude?',
                ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                ['Monday', 'Tuesday', 'Wednesday']
            )
            ->expectsConfirmation('Is this a round trip?', 'yes')
            ->expectsQuestion('Return date (beginning of range)', 'Jan 18')
            ->expectsQuestion('Return date (end of range)', 'Jan 25')
            ->expectsConfirmation('Would you like to exclude any days of the week from your range?', 'yes')
            ->expectsChoice(
                'Which days would you like to exclude?',
                ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                ['Monday', 'Tuesday', 'Wednesday']
            )
            ->expectsChoice('Would you like to exclude or explicitly include any airlines in your search?', 'exclude', ['exclude', 'include', 'none'])
            ->expectsQuestion('Which airlines? (e.g. British Airways, Emirates) ', 'British Airways, Emirates')
            ->expectsQuestion('How many adults?', 2)
            ->expectsQuestion('How many children?', 1)
            ->expectsQuestion('How many infants?', 0)
            ->expectsChoice('Cabin class', 'Economy', ['Economy', 'Premium Economy', 'Business', 'First'])
            ->expectsQuestion('Maximum stopovers', 1)
            ->expectsQuestion('Maximum duration in hours', 24)
            ->expectsChoice('What markets would you like links for?', 'Random', [
                'Random',
                'High GDP',
                'Medium GDP',
                'Low GDP',
            ])
            ->expectsQuestion('Market limit (higher = more links)', 10)
            ->expectsQuestion('Currency', 'CAD')
    }

    /** @test **/
    public function if_not_round_trip_return_date_prompts_should_not_be_shown(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function non_resultant_airline_search_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_no_airline_exclusion_is_desired_by_user_then_the_airlines_prompt_should_not_be_shown(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_adults_value_is_non_numeric_it_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_children_value_is_non_numeric_it_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_infants_value_is_non_numeric_it_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_stopovers_value_is_non_numeric_it_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_duration_value_is_non_numeric_it_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_market_limit_value_is_non_numeric_it_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }

    /** @test **/
    public function if_currency_value_is_non_existent_it_should_throw_an_error_code_1(): void
    {
        // Arrange

        // Act

        // Assert
    }
}
