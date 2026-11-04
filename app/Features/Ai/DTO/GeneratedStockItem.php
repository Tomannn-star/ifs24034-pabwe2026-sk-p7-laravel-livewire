<?php

namespace App\Features\Ai\DTO;

class GeneratedStockItem
{
    public function __construct(
        public string $name,
        public ?string $sku = null,
        public ?string $description = null,
        public int $quantity = 10,
        public string $unit = 'pcs',
        public ?float $price = null,
        public int $min_stock = 5,
        public string $status = 'available',
    ) {}

    /**
     * Convert the DTO to an associative array for persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'price' => $this->price,
            'min_stock' => $this->min_stock,
            'status' => $this->status,
        ];
    }

    /**
     * Create a DTO instance from an associative array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            sku: isset($data['sku']) && $data['sku'] !== '' ? (string) $data['sku'] : null,
            description: isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null,
            quantity: isset($data['quantity']) ? (int) $data['quantity'] : 10,
            unit: (string) ($data['unit'] ?? 'pcs'),
            price: isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : null,
            min_stock: isset($data['min_stock']) ? (int) $data['min_stock'] : 5,
            status: (string) ($data['status'] ?? 'available'),
        );
    }
}

