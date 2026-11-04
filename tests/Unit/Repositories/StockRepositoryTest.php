<?php

namespace Tests\Unit\Repositories;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Repositories\StockRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected StockRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new StockRepository();
    }

    public function test_create_and_find(): void
    {
        $user = User::factory()->create();
        $stock = $this->repository->create([
            'user_id' => $user->id,
            'name' => 'Kopi Robusta',
            'sku' => 'KOP-ROB-01',
            'description' => 'Biji kopi robusta',
            'quantity' => 20,
            'unit' => 'kg',
            'price' => 50000,
            'min_stock' => 5,
            'status' => 'available',
        ]);

        $this->assertInstanceOf(Stock::class, $stock);
        $this->assertDatabaseHas('stocks', ['id' => $stock->id, 'name' => 'Kopi Robusta']);

        $found = $this->repository->find($stock->id);
        $this->assertNotNull($found);
        $this->assertEquals($stock->id, $found->id);

        $foundOrFail = $this->repository->findOrFail($stock->id);
        $this->assertEquals($stock->id, $foundOrFail->id);
    }

    public function test_find_or_fail_throws_exception_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(9999);
    }

    public function test_update_and_delete(): void
    {
        $user = User::factory()->create();
        $stock = $this->repository->create([
            'user_id' => $user->id,
            'name' => 'Original Name',
            'quantity' => 10,
            'unit' => 'pcs',
            'min_stock' => 5,
            'status' => 'available',
        ]);

        $updated = $this->repository->update($stock, ['name' => 'Updated Name']);
        $this->assertTrue($updated);
        $this->assertDatabaseHas('stocks', ['id' => $stock->id, 'name' => 'Updated Name']);

        $deleted = $this->repository->delete($stock);
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    public function test_adjust_quantity(): void
    {
        $user = User::factory()->create();
        $stock = $this->repository->create([
            'user_id' => $user->id,
            'name' => 'Stock Test',
            'quantity' => 10,
            'unit' => 'pcs',
            'min_stock' => 5,
            'status' => 'available',
        ]);

        $adjusted = $this->repository->adjustQuantity($stock, 5);
        $this->assertEquals(15, $adjusted->quantity);
        $this->assertEquals(StockStatus::AVAILABLE, $adjusted->status);

        $adjusted2 = $this->repository->adjustQuantity($stock, -12);
        $this->assertEquals(3, $adjusted2->quantity);
        $this->assertEquals(StockStatus::LOW_STOCK, $adjusted2->status);
    }

    public function test_all_for_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Stock::factory()->count(3)->create(['user_id' => $user->id]);
        Stock::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $stocks = $this->repository->allForUser($user->id);
        $this->assertCount(3, $stocks);
    }

    public function test_for_user_with_filters(): void
    {
        $user = User::factory()->create();

        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Gula Pasir 1kg', 'sku' => 'GUL-01', 'status' => 'available']);
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Garam Dapur', 'sku' => 'GAR-02', 'status' => 'low_stock']);
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Minyak Goreng', 'sku' => 'MIN-03', 'description' => 'Gula cair', 'status' => 'out_of_stock']);

        // Without filter
        $all = $this->repository->forUser($user->id);
        $this->assertCount(3, $all);

        // Status filter
        $lowStock = $this->repository->forUser($user->id, status: 'low_stock');
        $this->assertCount(1, $lowStock);

        // Search filter by name
        $searchName = $this->repository->forUser($user->id, search: 'Gula');
        $this->assertCount(2, $searchName); // Gula Pasir (name) and Minyak Goreng (description)

        // Search filter by sku
        $searchSku = $this->repository->forUser($user->id, search: 'GAR-02');
        $this->assertCount(1, $searchSku);
    }

    public function test_insert_many(): void
    {
        $user = User::factory()->create();

        $emptyResult = $this->repository->insertMany([]);
        $this->assertTrue($emptyResult);

        $records = [
            ['user_id' => $user->id, 'name' => 'Item 1', 'quantity' => 10, 'unit' => 'pcs', 'min_stock' => 5, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $user->id, 'name' => 'Item 2', 'quantity' => 2, 'unit' => 'pcs', 'min_stock' => 5, 'status' => 'low_stock', 'created_at' => now(), 'updated_at' => now()],
        ];

        $result = $this->repository->insertMany($records);
        $this->assertTrue($result);
        $this->assertDatabaseCount('stocks', 2);
    }
}

