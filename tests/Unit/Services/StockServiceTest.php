<?php

namespace Tests\Unit\Services;

use App\Features\Stock\Contracts\StockRepositoryInterface;
use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Repositories\StockRepository;
use App\Features\Stock\Services\StockService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockService $service;
    protected StockRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new StockRepository();
        $this->service = new StockService($this->repository);
    }

    public function test_get_stocks_for_user(): void
    {
        $user = User::factory()->create();
        Stock::factory()->count(2)->create(['user_id' => $user->id]);

        $paginator = $this->service->getStocksForUser($user->id);
        $this->assertCount(2, $paginator);
    }

    public function test_create_stock(): void
    {
        $user = User::factory()->create();
        $stock = $this->service->createStock($user->id, [
            'name' => 'Beras Pandan Wangi',
            'sku' => 'BER-PW-01',
            'description' => 'Beras kualitas tinggi',
            'quantity' => 20,
            'unit' => 'kg',
            'price' => 15000,
            'min_stock' => 5,
            'status' => StockStatus::AVAILABLE, // test enum instance
        ]);

        $this->assertInstanceOf(Stock::class, $stock);
        $this->assertEquals('Beras Pandan Wangi', $stock->name);
        $this->assertEquals(StockStatus::AVAILABLE, $stock->status);
    }

    public function test_create_stock_auto_determines_low_and_out_of_stock(): void
    {
        $user = User::factory()->create();

        $low = $this->service->createStock($user->id, [
            'name' => 'Barang Low',
            'quantity' => 3,
            'min_stock' => 5,
        ]);
        $this->assertEquals(StockStatus::LOW_STOCK, $low->status);

        $out = $this->service->createStock($user->id, [
            'name' => 'Barang Out',
            'quantity' => 0,
            'min_stock' => 5,
        ]);
        $this->assertEquals(StockStatus::OUT_OF_STOCK, $out->status);
    }

    public function test_update_stock(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

        $updated = $this->service->updateStock($stock, [
            'name' => 'Updated Name',
            'quantity' => 2,
            'min_stock' => 5,
            'status' => StockStatus::LOW_STOCK, // test enum instance
        ]);

        $this->assertTrue($updated);
        $stock->refresh();
        $this->assertEquals('Updated Name', $stock->name);
        $this->assertEquals(StockStatus::LOW_STOCK, $stock->status);
    }

    public function test_adjust_quantity(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create([
            'user_id' => $user->id,
            'quantity' => 10,
            'min_stock' => 5,
            'status' => 'available',
        ]);

        $this->service->adjustQuantity($stock, -6);
        $stock->refresh();
        $this->assertEquals(4, $stock->quantity);
        $this->assertEquals(StockStatus::LOW_STOCK, $stock->status);
    }

    public function test_cover_photo_management(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $stock = Stock::factory()->create(['user_id' => $user->id]);

        $fakeFile = UploadedFile::fake()->image('cover.jpg');

        // Update cover
        $path = $this->service->updateCoverPhoto($stock, $fakeFile);
        $stock->refresh();
        $this->assertEquals($path, $stock->cover_image_path);
        Storage::disk('public')->assertExists($path);

        // Update cover again (replaces old)
        $newFakeFile = UploadedFile::fake()->image('new-cover.jpg');
        $newPath = $this->service->updateCoverPhoto($stock, $newFakeFile);
        $stock->refresh();
        $this->assertEquals($newPath, $stock->cover_image_path);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertExists($newPath);

        // Delete cover
        $deleted = $this->service->deleteCoverPhoto($stock);
        $this->assertTrue($deleted);
        $stock->refresh();
        $this->assertNull($stock->cover_image_path);
        Storage::disk('public')->assertMissing($newPath);

        // Delete stock cleans up cover photo if present
        $anotherFile = UploadedFile::fake()->image('another.jpg');
        $anotherPath = $this->service->updateCoverPhoto($stock, $anotherFile);
        Storage::disk('public')->assertExists($anotherPath);

        $this->service->deleteStock($stock);
        Storage::disk('public')->assertMissing($anotherPath);
        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }
}
