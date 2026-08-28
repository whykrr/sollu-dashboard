<?php

namespace Tests\Unit\Services\App\Reports;

use App\Models\Master\Product;
use App\Models\Master\ProductCategory;
use App\Models\Outlet;
use App\Models\Sales\Transaction;
use App\Models\Sales\TransactionItem;
use App\Models\User;
use App\Services\App\Reports\ProductReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductReportService();
    }

    public function test_it_gets_report()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Report',
        ]);

        $category = ProductCategory::create([
            'business_id' => $user->business_id,
            'name' => 'Food',
        ]);

        $product = Product::create([
            'business_id' => $user->business_id,
            'product_category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'product_type' => 'basic',
        ]);

        $now = Carbon::now();
        
        $transaction = Transaction::create([
            'outlet_id' => $outlet->id,
            'status' => 'completed',
            'subtotal' => 100000,
            'total' => 100000,
            'transaction_number' => 'TRX-101',
            'created_at' => $now,
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => 'Nasi Goreng',
            'qty' => 2,
            'price' => 50000,
            'subtotal' => 100000,
        ]);

        $startDate = $now->copy()->subDay();
        $endDate = $now->copy()->addDay();

        $result = $this->service->getReport($outlet->id, $startDate, $endDate);

        $this->assertNotEmpty($result->items());
        $firstItem = $result->items()[0];

        $this->assertEquals('Nasi Goreng', $firstItem->product_name);
        $this->assertEquals('Food', $firstItem->category_name);
        $this->assertEquals(2, $firstItem->total_qty);
        $this->assertEquals(100000, $firstItem->total_sales);
    }
}
