<?php

namespace Tests\Unit\Services;

use App\Jobs\ProcessProductImage;
use App\Models\Product;
use App\Services\SpreadsheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SpreadsheetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SpreadsheetService $spreadsheetService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->spreadsheetService = new SpreadsheetService();
    }

    /** @test */
    public function it_processes_valid_spreadsheet_data()
    {
        // Arrange: Set up a mock of the imported data
        $filePath = 'path/to/spreadsheet.xlsx'; // Simulated file path
        $productsData = [
            ['product_code' => 'P001', 'quantity' => 10],
            ['product_code' => 'P002', 'quantity' => 5],
        ];

        app()->instance('importer', new class($productsData) {
            private array $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function import($filePath)
            {
                return $this->data;
            }
        });

        // Act: Process the spreadsheet
        Queue::fake(); // Fake the queue to avoid actual job dispatching
        $this->spreadsheetService->processSpreadsheet($filePath);

        // Assert: Check that products were created
        $this->assertCount(2, Product::all());
        $this->assertDatabaseHas('products', ['code' => 'P001', 'quantity' => 10]);
        $this->assertDatabaseHas('products', ['code' => 'P002', 'quantity' => 5]);

        // Assert: Check that the ProcessProductImage job was dispatched
        Queue::assertPushed(ProcessProductImage::class, 2);
    }

    /** @test */
    public function it_skips_invalid_spreadsheet_data()
    {
        // Arrange: Set up mock of the imported data with invalid data
        $filePath = 'path/to/spreadsheet.xlsx'; // Simulated file path
        $productsData = [
            ['product_code' => 'P001', 'quantity' => 10],
            ['product_code' => '', 'quantity' => 5], // Invalid: missing product_code
            ['product_code' => 'P001', 'quantity' => -1], // Invalid: quantity < 1
        ];

        app()->instance('importer', new class($productsData) {
            private array $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function import($filePath)
            {
                return $this->data;
            }
        });

        // Act: Process the spreadsheet
        Queue::fake(); // Fake the queue to avoid actual job dispatching
        $this->spreadsheetService->processSpreadsheet($filePath);

        // Assert: Check that only one product was created
        $this->assertCount(1, Product::all());
        $this->assertDatabaseHas('products', ['code' => 'P001', 'quantity' => 10]);

        // Assert: Check that no job was dispatched for invalid data
        Queue::assertPushed(ProcessProductImage::class, 1);
    }
}