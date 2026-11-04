<?php

namespace Tests\Unit\DTO;

use App\Features\Ai\DTO\GeneratedStockItem;
use PHPUnit\Framework\TestCase;

class GeneratedStockItemTest extends TestCase
{
    public function test_dto_properties_and_to_array(): void
    {
        $dto = new GeneratedStockItem(
            name: 'Kopi Arabika 250g',
            sku: 'COF-ARB-250',
            description: 'Kopi biji sangrai kualitas premium',
            quantity: 25,
            unit: 'pack',
            price: 75000.0,
            min_stock: 5,
            status: 'available'
        );

        $this->assertEquals('Kopi Arabika 250g', $dto->name);
        $this->assertEquals('COF-ARB-250', $dto->sku);
        $this->assertEquals('Kopi biji sangrai kualitas premium', $dto->description);
        $this->assertEquals(25, $dto->quantity);
        $this->assertEquals('pack', $dto->unit);
        $this->assertEquals(75000.0, $dto->price);
        $this->assertEquals(5, $dto->min_stock);
        $this->assertEquals('available', $dto->status);

        $array = $dto->toArray();
        $this->assertEquals([
            'name' => 'Kopi Arabika 250g',
            'sku' => 'COF-ARB-250',
            'description' => 'Kopi biji sangrai kualitas premium',
            'quantity' => 25,
            'unit' => 'pack',
            'price' => 75000.0,
            'min_stock' => 5,
            'status' => 'available',
        ], $array);
    }

    public function test_from_array_with_defaults(): void
    {
        $dto = GeneratedStockItem::fromArray([
            'name' => 'Barang Minimal',
        ]);

        $this->assertEquals('Barang Minimal', $dto->name);
        $this->assertNull($dto->sku);
        $this->assertNull($dto->description);
        $this->assertEquals(10, $dto->quantity);
        $this->assertEquals('pcs', $dto->unit);
        $this->assertNull($dto->price);
        $this->assertEquals(5, $dto->min_stock);
        $this->assertEquals('available', $dto->status);
    }
}

