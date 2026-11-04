<?php

namespace Database\Factories;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    protected $model = Stock::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->bothify('SKU-###??')),
            'description' => fake()->paragraph(),
            'quantity' => fake()->numberBetween(10, 50),
            'unit' => 'pcs',
            'price' => fake()->randomFloat(2, 5000, 250000),
            'min_stock' => 5,
            'status' => StockStatus::AVAILABLE->value,
            'cover_image_path' => null,
        ];
    }

    /**
     * Indicate that the stock is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 20,
            'min_stock' => 5,
            'status' => StockStatus::AVAILABLE->value,
        ]);
    }

    /**
     * Indicate that the stock is low.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 3,
            'min_stock' => 5,
            'status' => StockStatus::LOW_STOCK->value,
        ]);
    }

    /**
     * Indicate that the stock is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0,
            'min_stock' => 5,
            'status' => StockStatus::OUT_OF_STOCK->value,
        ]);
    }
}
