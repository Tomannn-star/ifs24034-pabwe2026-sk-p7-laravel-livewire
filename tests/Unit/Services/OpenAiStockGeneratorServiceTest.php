<?php

namespace Tests\Unit\Services;

use App\Features\Ai\Services\OpenAiStockGeneratorService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class OpenAiStockGeneratorServiceTest extends TestCase
{
    public function test_empty_prompt_throws_exception(): void
    {
        $service = new OpenAiStockGeneratorService('https://api.openai.com/v1', 'mock-key', 'gpt-4o-mini');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Prompt tidak boleh kosong.');
        $service->generateStocks('   ');
    }

    public function test_successful_generation(): void
    {
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'stocks' => [
                                    [
                                        'name' => 'Kopi Robusta',
                                        'sku' => 'ROB-01',
                                        'description' => 'Biji sangrai',
                                        'quantity' => 15,
                                        'unit' => 'kg',
                                        'price' => 50000,
                                        'min_stock' => 5,
                                    ],
                                    [
                                        'name' => 'Teh Melati',
                                        'sku' => null,
                                        'description' => null,
                                        'quantity' => 3,
                                        'unit' => 'box',
                                        'price' => null,
                                        'min_stock' => 5,
                                    ],
                                ],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new OpenAiStockGeneratorService('https://api.openai.com/v1', 'mock-key', 'gpt-4o-mini');
        $results = $service->generateStocks('Buatkan 2 stok', 2);

        $this->assertCount(2, $results);
        $this->assertEquals('Kopi Robusta', $results[0]->name);
        $this->assertEquals('ROB-01', $results[0]->sku);
        $this->assertEquals(15, $results[0]->quantity);
        $this->assertEquals('available', $results[0]->status);

        $this->assertEquals('Teh Melati', $results[1]->name);
        $this->assertNull($results[1]->sku);
        $this->assertNull($results[1]->description);
        $this->assertEquals(3, $results[1]->quantity);
        $this->assertEquals('low_stock', $results[1]->status);
    }

    public function test_api_failure_throws_exception(): void
    {
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'error' => [
                    'message' => 'Invalid API Key provided',
                ],
            ], 401),
        ]);

        $service = new OpenAiStockGeneratorService('https://api.openai.com/v1', 'mock-key', 'gpt-4o-mini');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('OpenAI API Error: Invalid API Key provided');
        $service->generateStocks('Stok alat tulis');
    }

    public function test_parse_content_with_markdown_fences(): void
    {
        $service = new OpenAiStockGeneratorService('https://api.openai.com/v1', 'mock-key', 'gpt-4o-mini');
        $raw = "```json\n[{\"name\": \"Code Fence Stock\", \"quantity\": 12}]\n```";

        $results = $service->parseContent($raw);
        $this->assertCount(1, $results);
        $this->assertEquals('Code Fence Stock', $results[0]->name);
        $this->assertEquals(12, $results[0]->quantity);
    }

    public function test_parse_content_invalid_json(): void
    {
        $service = new OpenAiStockGeneratorService('https://api.openai.com/v1', 'mock-key', 'gpt-4o-mini');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('AI mengembalikan format respon yang tidak valid.');
        $service->parseContent('Not valid JSON');
    }

    public function test_parse_content_empty_stocks(): void
    {
        $service = new OpenAiStockGeneratorService('https://api.openai.com/v1', 'mock-key', 'gpt-4o-mini');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tidak ada stok barang yang berhasil di-generate dari AI.');
        $service->parseContent('{"stocks": []}');
    }

    public function test_parse_content_no_valid_items(): void
    {
        $service = new OpenAiStockGeneratorService('https://api.openai.com/v1', 'mock-key', 'gpt-4o-mini');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Format item stok yang dikembalikan AI tidak sesuai spesifikasi.');
        $service->parseContent('{"stocks": [{"bad_key": "val"}]}');
    }

    public function test_constructor_handles_swapped_endpoint_and_key(): void
    {
        Http::fake([
            'https://ai.example.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => '{"stocks":[{"name":"Barang Swapped"}]}']]],
            ]),
        ]);

        $service = new OpenAiStockGeneratorService('sk-secret-key', 'https://ai.example.com/v1');
        $stocks = $service->generateStocks('Rencana');
        $this->assertCount(1, $stocks);
    }

    public function test_constructor_auto_prefixes_https(): void
    {
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => '{"stocks":[{"name":"Barang Prefix"}]}']]],
            ]),
        ]);

        $service = new OpenAiStockGeneratorService('api.openai.com/v1', 'mock-key');
        $stocks = $service->generateStocks('Rencana');
        $this->assertCount(1, $stocks);
    }
}

