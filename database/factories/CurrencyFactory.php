<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cryptocurrencies = config('services.cryptocurrencies');

        $randomCurrency = $this->faker->unique()->randomElement($cryptocurrencies);

        return [
            'name' => $randomCurrency,
        ];
    }
}
