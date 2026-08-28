<?php

namespace Tests\Unit\Services\App\Reports;

use App\Models\Master\InventoryItem;
use App\Models\Master\PaymentMethod;
use App\Models\Master\Product;
use App\Models\Master\ProductCategory;
use App\Models\Outlet;
use App\Models\Sales\Transaction;
use App\Models\Sales\TransactionItem;
use App\Models\Sales\TransactionPayment;
use App\Models\User;
use App\Services\App\Reports\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
    }

    public function test_it_returns_dashboard_metrics_and_trends()
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

        $paymentMethod = PaymentMethod::create([
            'business_id' => $user->business_id,
            'name' => 'Cash',
            'type' => 'cash',
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

        TransactionPayment::create([
            'transaction_id' => $transaction->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => 100000,
        ]);

        $invItem = new InventoryItem([
            'business_id' => $user->business_id,
            'name' => 'Beras',
            'item_type' => 'raw_material',
        ]);
        $invItem->minimum_stock = 10;
        $invItem->save();

        DB::table('inventory_balances')->insert([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'business_id' => $user->business_id,
            'outlet_id' => $outlet->id,
            'inventory_item_id' => $invItem->id,
            'current_stock' => 5, // Below min stock
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $startDate = $now->copy()->startOfDay();
        $endDate = $now->copy()->endOfDay();
        $prevStartDate = $now->copy()->subDay()->startOfDay();
        $prevEndDate = $now->copy()->subDay()->endOfDay();

        // 1. Metrics
        $metrics = $this->service->getMetrics($outlet->id, $startDate, $endDate, $prevStartDate, $prevEndDate);
        $this->assertEquals(100000, $metrics['totalSales']['now']);
        $this->assertEquals(1, $metrics['totalTransactions']['now']);
        $this->assertEquals(100000, $metrics['averageSales']['now']);

        // 2. Sales Trend (Today)
        $trend = $this->service->getSalesTrend([$outlet->id], $startDate, $endDate, $prevStartDate, $prevEndDate, true);
        $this->assertCount(24, $trend['label']);
        
        // 3. Category Sales Trend
        $categoryTrend = $this->service->getCategorySalesTrend([$outlet->id], $startDate, $endDate);
        $this->assertContains('Food', $categoryTrend['label']);
        $this->assertContains(100000.0, $categoryTrend['value']);

        // 4. Payment Method Summary
        $paymentSummary = $this->service->getPaymentMethodSummary([$outlet->id], $startDate, $endDate);
        $this->assertContains('Cash', $paymentSummary['label']);
        $this->assertContains(1, $paymentSummary['value']);

        // 5. Most Sold
        $mostSold = $this->service->getMostSoldProducts([$outlet->id], $startDate, $endDate);
        $this->assertCount(1, $mostSold);
        $this->assertEquals('Nasi Goreng', $mostSold[0]['name']);

        // 6. Low Stock
        $lowStock = $this->service->getLowStockProducts([$outlet->id]);
        $this->assertCount(1, $lowStock);
        $this->assertEquals('Beras', $lowStock[0]['name']);

        // 7. Not Sold
        $unsoldProduct = Product::create([
            'business_id' => $user->business_id,
            'name' => 'Mie Goreng',
            'product_type' => 'basic',
        ]);
        $notSold = $this->service->getProductNotSold([$outlet->id], $startDate, $endDate);
        $this->assertCount(1, $notSold);
        $this->assertEquals('Mie Goreng', $notSold[0]['name']);
    }
}
