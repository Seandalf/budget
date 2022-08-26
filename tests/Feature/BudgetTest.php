<?php

namespace Tests\Feature\Auth;

use App\Models\Currency;
use App\Models\TimePeriod;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    public function test_a_budget_can_be_created()
    {
        $user = $this->create_user_with_role();

        $currency = Currency::first();
        $time_period = TimePeriod::whereName('weekly')->first();
        $total_intervals = rand(6, 24);
        $budget_name = fake()->word();

        $response = $this->actingAs($user)->json('PUT', '/api/budgets/create', [
            'name' => $budget_name,
            'description' => fake()->sentence(6),
            'opening_balance'    => rand(1000,10000),
            'closing_balance'    => rand(10000,20000),
            'future_intervals'   => $total_intervals,
            'currency_id'        => $currency->id,
            'time_period_id'     => $time_period->id,
            'time_period_amount' => null,
            'starts_at'          => now()->addDays(10)->toDateString(),
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('intervals', $total_intervals);
    }

    public function test_the_correct_number_of_intervals_are_created_when_a_budget_is_created()
    {
        $user = $this->create_user_with_role();

        $currency = Currency::first();
        $time_period = TimePeriod::whereName('weekly')->first();
        $total_intervals = rand(6, 24);

        $response = $this->actingAs($user)->json('PUT', '/api/budgets/create', [
            'name' => fake()->word(),
            'description' => fake()->sentence(6),
            'opening_balance'    => rand(1000,10000),
            'closing_balance'    => rand(10000,20000),
            'future_intervals'   => $total_intervals,
            'currency_id'        => $currency->id,
            'time_period_id'     => $time_period->id,
            'time_period_amount' => null,
            'starts_at'          => now()->addDays(10)->toDateString(),
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('intervals', $total_intervals);
    }
}
