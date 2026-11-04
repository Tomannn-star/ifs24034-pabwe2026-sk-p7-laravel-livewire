<?php

namespace Database\Seeders;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ]
        );

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Member User',
                'username' => 'member',
                'password' => Hash::make('member'),
                'role' => 'member',
            ]
        );

        Stock::firstOrCreate(
            ['user_id' => $member->id, 'name' => 'Kertas HVS A4 80gr'],
            [
                'sku' => 'ATK-HVS-001',
                'description' => 'Kertas HVS ukuran A4 80 gram untuk printer dan fotokopi.',
                'quantity' => 25,
                'unit' => 'rim',
                'price' => 55000,
                'min_stock' => 5,
                'status' => StockStatus::AVAILABLE->value,
            ]
        );

        Stock::firstOrCreate(
            ['user_id' => $member->id, 'name' => 'Spidol Whiteboard Hitam'],
            [
                'sku' => 'ATK-SPD-002',
                'description' => 'Spidol papan tulis tinta hitam mudah dihapus.',
                'quantity' => 4,
                'unit' => 'pcs',
                'price' => 12000,
                'min_stock' => 5,
                'status' => StockStatus::LOW_STOCK->value,
            ]
        );

        Stock::firstOrCreate(
            ['user_id' => $member->id, 'name' => 'Tinta Printer Epson 003 Black'],
            [
                'sku' => 'TIN-EPS-003',
                'description' => 'Tinta original Epson 003 Black untuk printer L3110 / L3150.',
                'quantity' => 0,
                'unit' => 'botol',
                'price' => 85000,
                'min_stock' => 3,
                'status' => StockStatus::OUT_OF_STOCK->value,
            ]
        );
    }
}
