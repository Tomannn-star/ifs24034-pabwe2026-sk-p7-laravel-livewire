<?php

namespace Tests\Feature\Stock;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\DTO\GeneratedStockItem;
use App\Features\Stock\Livewire\StockList;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class StockListLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_stocks(): void
    {
        $this->get('/stocks')->assertRedirect(route('login'));
    }

    public function test_member_can_view_own_stocks(): void
    {
        $user = User::factory()->member()->create();
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Barang Khusus Member']);

        $otherUser = User::factory()->member()->create();
        Stock::factory()->create(['user_id' => $otherUser->id, 'name' => 'Barang Rahasia Orang Lain']);

        $this->actingAs($user);

        Livewire::test(StockList::class)
            ->assertSee('Barang Khusus Member')
            ->assertDontSee('Barang Rahasia Orang Lain');
    }

    public function test_create_stock_validation_and_success(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        // Validation error
        Livewire::test(StockList::class)
            ->call('openCreateModal')
            ->set('formName', '')
            ->set('formStatus', 'invalid_status')
            ->call('saveStock')
            ->assertHasErrors(['formName', 'formStatus']);

        // Success
        Livewire::test(StockList::class)
            ->call('openCreateModal')
            ->set('formName', 'Kertas HVS A4 80g')
            ->set('formSku', 'HVS-A4-80')
            ->set('formDescription', 'Kertas HVS 1 rim')
            ->set('formQuantity', 25)
            ->set('formUnit', 'rim')
            ->set('formPrice', 45000)
            ->set('formMinStock', 5)
            ->set('formStatus', 'available')
            ->call('saveStock')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Kertas HVS A4 80g',
            'sku' => 'HVS-A4-80',
            'quantity' => 25,
            'unit' => 'rim',
        ]);
    }

    public function test_edit_stock_success(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $stock = Stock::factory()->create(['user_id' => $user->id, 'name' => 'Nama Lama']);

        Livewire::test(StockList::class)
            ->call('openEditModal', $stock->id)
            ->assertSet('editingStockId', $stock->id)
            ->assertSet('formName', 'Nama Lama')
            ->set('formName', 'Nama Telah Diedit')
            ->call('saveStock')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('stocks', [
            'id' => $stock->id,
            'name' => 'Nama Telah Diedit',
        ]);
    }

    public function test_increment_and_decrement_quantity(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $stock = Stock::factory()->create([
            'user_id' => $user->id,
            'quantity' => 10,
            'min_stock' => 5,
            'status' => 'available',
        ]);

        Livewire::test(StockList::class)
            ->call('incrementQuantity', $stock->id);

        $this->assertEquals(11, $stock->fresh()->quantity);

        Livewire::test(StockList::class)
            ->call('decrementQuantity', $stock->id);

        $this->assertEquals(10, $stock->fresh()->quantity);
    }

    public function test_delete_stock(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $stock = Stock::factory()->create(['user_id' => $user->id]);

        Livewire::test(StockList::class)
            ->call('deleteStock', $stock->id);

        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    public function test_confirm_and_cancel_delete_stock(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $stock = Stock::factory()->create(['user_id' => $user->id]);

        Livewire::test(StockList::class)
            ->assertSet('confirmingStockDeletionId', null)
            ->call('confirmDelete', $stock->id)
            ->assertSet('confirmingStockDeletionId', $stock->id)
            ->call('cancelDelete')
            ->assertSet('confirmingStockDeletionId', null)
            ->call('confirmDelete', $stock->id)
            ->call('deleteStock')
            ->assertSet('confirmingStockDeletionId', null);

        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    public function test_search_and_status_filter(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Tinta Printer Epson', 'status' => 'available']);
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Tinta Printer Canon', 'status' => 'out_of_stock']);

        Livewire::test(StockList::class)
            ->set('search', 'Epson')
            ->assertSee('Tinta Printer Epson')
            ->assertDontSee('Tinta Printer Canon')
            ->set('search', '')
            ->set('statusFilter', 'out_of_stock')
            ->assertSee('Tinta Printer Canon')
            ->assertDontSee('Tinta Printer Epson');
    }

    public function test_export_excel(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Exportable Stock Item']);

        Livewire::test(StockList::class)
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    public function test_import_excel(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'SKU');
        $sheet->setCellValue('D1', 'Deskripsi');
        $sheet->setCellValue('E1', 'Jumlah');
        $sheet->setCellValue('F1', 'Satuan');
        $sheet->setCellValue('G1', 'Harga');
        $sheet->setCellValue('H1', 'Min Stok');
        $sheet->setCellValue('I1', 'Status');

        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', 'Imported Excel Stock');
        $sheet->setCellValue('C2', 'EXC-01');
        $sheet->setCellValue('D2', 'Deskripsi Excel');
        $sheet->setCellValue('E2', '35');
        $sheet->setCellValue('F2', 'pcs');
        $sheet->setCellValue('G2', '12500');
        $sheet->setCellValue('H2', '5');
        $sheet->setCellValue('I2', 'available');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_upload_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $fileContent = file_get_contents($tempPath);
        @unlink($tempPath);

        $uploadedFile = UploadedFile::fake()->createWithContent('stocks.xlsx', $fileContent);

        Livewire::test(StockList::class)
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->set('importFile', $uploadedFile)
            ->call('importExcel')
            ->assertSet('showImportModal', false);

        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Imported Excel Stock',
            'sku' => 'EXC-01',
            'quantity' => 35,
        ]);
    }

    public function test_ai_generation_and_saving(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $mockAiClient = $this->createStub(AiClientInterface::class);
        $mockAiClient->method('generateStocks')->willReturn([
            new GeneratedStockItem('AI Stock 1', 'SKU-AI-1', 'Desc 1', 20, 'pcs', 10000.0, 5, 'available'),
            new GeneratedStockItem('AI Stock 2', null, null, 15, 'box', null, 5, 'available'),
        ]);
        $this->app->instance(AiClientInterface::class, $mockAiClient);

        // Validation for prompt and count
        Livewire::test(StockList::class)
            ->call('openAiModal')
            ->set('aiPrompt', '')
            ->set('aiCount', 0)
            ->call('generateWithAi')
            ->assertHasErrors(['aiPrompt', 'aiCount']);

        $component = Livewire::test(StockList::class)
            ->call('openAiModal')
            ->assertSet('showAiModal', true)
            ->set('aiPrompt', 'Buatkan saya inventaris kafe')
            ->set('aiCount', 2)
            ->call('generateWithAi')
            ->assertCount('aiGeneratedItems', 2)
            ->assertCount('aiSelectedIndices', 2);

        // Saving with no items selected
        $component->set('aiSelectedIndices', [])
            ->call('saveAiGenerated')
            ->assertSet('aiErrorMessage', 'Pilih minimal satu item stok yang ingin disimpan.');

        // Save selected items
        $component->set('aiSelectedIndices', [0, 1])
            ->call('saveAiGenerated')
            ->assertSet('showAiModal', false);

        $this->assertDatabaseHas('stocks', ['user_id' => $user->id, 'name' => 'AI Stock 1']);
        $this->assertDatabaseHas('stocks', ['user_id' => $user->id, 'name' => 'AI Stock 2']);
    }

    public function test_ai_generation_handles_exception(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $mockAiClient = $this->createStub(AiClientInterface::class);
        $mockAiClient->method('generateStocks')->willThrowException(new \RuntimeException('API quota exceeded'));
        $this->app->instance(AiClientInterface::class, $mockAiClient);

        Livewire::test(StockList::class)
            ->call('openAiModal')
            ->set('aiPrompt', 'Error test')
            ->call('generateWithAi')
            ->assertSet('aiErrorMessage', 'API quota exceeded');
    }

    public function test_close_modals(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        Livewire::test(StockList::class)
            ->call('openCreateModal')
            ->call('closeFormModal')
            ->assertSet('showFormModal', false)
            ->call('openImportModal')
            ->call('closeImportModal')
            ->assertSet('showImportModal', false)
            ->call('openAiModal')
            ->call('closeAiModal')
            ->assertSet('showAiModal', false);
    }
}

