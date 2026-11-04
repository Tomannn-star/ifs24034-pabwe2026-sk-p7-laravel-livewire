<?php

namespace Tests\Unit\Services;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Repositories\StockRepository;
use App\Features\Stock\Services\StockExcelService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class StockExcelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockExcelService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StockExcelService(new StockRepository());
    }

    public function test_build_spreadsheet_and_export_response(): void
    {
        $user = User::factory()->create();
        $stocks = Stock::factory()->count(2)->create(['user_id' => $user->id]);

        // Add a mock or raw stock item with string status
        $rawStock = new Stock([
            'name' => 'Raw Item',
            'quantity' => 10,
            'unit' => 'pcs',
            'min_stock' => 5,
        ]);
        $rawStock->status = 'available';
        $stocks->push($rawStock);

        $spreadsheet = $this->service->buildSpreadsheet($stocks, 'Custom Stocks');
        $this->assertInstanceOf(Spreadsheet::class, $spreadsheet);
        $this->assertEquals('Custom Stocks', $spreadsheet->getActiveSheet()->getTitle());

        $response = $this->service->exportToResponse($stocks, 'test_stocks.xlsx');
        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        ob_start();
        $response->sendContent();
        $outputContent = ob_get_clean();
        $this->assertNotEmpty($outputContent);
    }

    public function test_import_spreadsheet(): void
    {
        $user = User::factory()->create();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'SKU');
        $sheet->setCellValue('D1', 'Deskripsi');
        $sheet->setCellValue('E1', 'Jumlah');
        $sheet->setCellValue('F1', 'Satuan');
        $sheet->setCellValue('G1', 'Harga');
        $sheet->setCellValue('H1', 'Min Stok');
        $sheet->setCellValue('I1', 'Status');

        // Row 2: Standard stock (tersedia)
        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', 'Kopi Kapal Api');
        $sheet->setCellValue('C2', 'KAP-01');
        $sheet->setCellValue('D2', 'Kopi sachet');
        $sheet->setCellValue('E2', '50');
        $sheet->setCellValue('F2', 'sachet');
        $sheet->setCellValue('G2', '1500');
        $sheet->setCellValue('H2', '10');
        $sheet->setCellValue('I2', 'tersedia');

        // Row 3: Status low_stock, description '-'
        $sheet->setCellValue('A3', '2');
        $sheet->setCellValue('B3', 'Teh Celup');
        $sheet->setCellValue('C3', 'TEH-01');
        $sheet->setCellValue('D3', '-');
        $sheet->setCellValue('E3', '3');
        $sheet->setCellValue('F3', 'box');
        $sheet->setCellValue('G3', '5000');
        $sheet->setCellValue('H3', '5');
        $sheet->setCellValue('I3', 'stok menipis');

        // Row 4: Column B empty, fallback to Column A, status habis
        $sheet->setCellValue('A4', 'Gula Merah');
        $sheet->setCellValue('B4', '');
        $sheet->setCellValue('C4', '-');
        $sheet->setCellValue('D4', 'Gula jawa');
        $sheet->setCellValue('E4', '0');
        $sheet->setCellValue('F4', 'kg');
        $sheet->setCellValue('G4', '-');
        $sheet->setCellValue('H4', '5');
        $sheet->setCellValue('I4', 'habis');

        // Row 5: Fallback status based on quantity (quantity = 25, min_stock = 5 -> available)
        $sheet->setCellValue('A5', '5');
        $sheet->setCellValue('B5', 'Biskuit Roma');
        $sheet->setCellValue('C5', 'ROM-01');
        $sheet->setCellValue('D5', 'Snack');
        $sheet->setCellValue('E5', '25');
        $sheet->setCellValue('F5', 'bungkus');
        $sheet->setCellValue('G5', '8000');
        $sheet->setCellValue('H5', '5');
        $sheet->setCellValue('I5', 'unknown_status');

        // Row 6: Header row duplicate to be skipped
        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Nama Barang');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_excel_stock_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $result = $this->service->import($tempPath, $user->id);
        @unlink($tempPath);

        $this->assertEquals(4, $result['imported']);
        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Kopi Kapal Api',
            'sku' => 'KAP-01',
            'quantity' => 50,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Teh Celup',
            'description' => null,
            'quantity' => 3,
            'status' => 'low_stock',
        ]);
        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Gula Merah',
            'sku' => null,
            'quantity' => 0,
            'status' => 'out_of_stock',
        ]);
        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Biskuit Roma',
            'status' => 'available',
        ]);
    }
}
