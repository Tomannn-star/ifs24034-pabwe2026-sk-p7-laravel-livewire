<?php

namespace Tests\Unit\Models;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $stock->user);
        $this->assertEquals($user->id, $stock->user->id);
    }

    public function test_stock_casts_and_defaults(): void
    {
        $stock = Stock::factory()->create([
            'quantity' => 15,
            'price' => 25000.50,
            'min_stock' => 5,
            'status' => 'available',
        ]);

        $this->assertIsInt($stock->quantity);
        $this->assertEquals(15, $stock->quantity);
        $this->assertInstanceOf(StockStatus::class, $stock->status);
        $this->assertEquals(StockStatus::AVAILABLE, $stock->status);
    }

    public function test_stock_factory_states(): void
    {
        $availableStock = Stock::factory()->available()->create();
        $this->assertEquals(StockStatus::AVAILABLE, $availableStock->status);

        $lowStock = Stock::factory()->lowStock()->create();
        $this->assertEquals(StockStatus::LOW_STOCK, $lowStock->status);

        $outOfStock = Stock::factory()->outOfStock()->create();
        $this->assertEquals(StockStatus::OUT_OF_STOCK, $outOfStock->status);
    }

    public function test_adjust_quantity_and_sync_status(): void
    {
        $stock = Stock::factory()->create([
            'quantity' => 10,
            'min_stock' => 5,
            'status' => StockStatus::AVAILABLE->value,
        ]);

        // Reduce to low stock (quantity = 5)
        $stock->adjustQuantity(-5);
        $stock->refresh();
        $this->assertEquals(5, $stock->quantity);
        $this->assertEquals(StockStatus::LOW_STOCK, $stock->status);

        // Reduce to out of stock (quantity = 0)
        $stock->adjustQuantity(-5);
        $stock->refresh();
        $this->assertEquals(0, $stock->quantity);
        $this->assertEquals(StockStatus::OUT_OF_STOCK, $stock->status);

        // Increase below 0 stays 0
        $stock->adjustQuantity(-2);
        $stock->refresh();
        $this->assertEquals(0, $stock->quantity);
        $this->assertEquals(StockStatus::OUT_OF_STOCK, $stock->status);

        // Increase back to available (quantity = 8)
        $stock->adjustQuantity(8);
        $stock->refresh();
        $this->assertEquals(8, $stock->quantity);
        $this->assertEquals(StockStatus::AVAILABLE, $stock->status);

        // Direct syncStatus call
        $stock->quantity = 1;
        $stock->syncStatus();
        $this->assertEquals(StockStatus::LOW_STOCK, $stock->status);
    }

    public function test_cover_image_url_returns_correct_url_or_null(): void
    {
        $stockWithoutCover = Stock::factory()->create(['cover_image_path' => null]);
        $this->assertNull($stockWithoutCover->coverImageUrl());

        Storage::fake('public');
        Storage::disk('public')->put('stock-covers/sample.png', 'content');

        $stockWithCover = Stock::factory()->create(['cover_image_path' => 'stock-covers/sample.png']);
        $this->assertNotNull($stockWithCover->coverImageUrl());
        $this->assertStringContainsString('stock-covers/sample.png', $stockWithCover->coverImageUrl());
    }
}
