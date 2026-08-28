<?php

namespace Tests\Unit\Services\App\Reports;

use App\Models\Master\PaymentMethod;
use App\Models\Outlet;
use App\Models\Sales\Transaction;
use App\Models\Sales\TransactionPayment;
use App\Models\User;
use App\Services\App\Reports\SalesReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SalesReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SalesReportService();
    }

    public function test_it_gets_report()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Report',
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
            'discount_amount' => 10000,
            'tax_amount' => 5000,
            'total' => 95000,
            'transaction_number' => 'TRX-101',
            'created_at' => $now,
        ]);

        TransactionPayment::create([
            'transaction_id' => $transaction->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => 95000,
        ]);

        $startDate = $now->copy()->startOfDay();
        $endDate = $now->copy()->endOfDay();

        $result = $this->service->getReport($outlet->id, $startDate, $endDate);

        $this->assertArrayHasKey('daily_sales', $result);
        $this->assertArrayHasKey('payment_methods', $result);

        $dailySales = $result['daily_sales'];
        $this->assertNotEmpty($dailySales->items());
        $firstDaily = $dailySales->items()[0];

        $this->assertEquals(100000, $firstDaily->gross_sales);
        $this->assertEquals(10000, $firstDaily->total_discount);
        $this->assertEquals(5000, $firstDaily->total_tax);
        $this->assertEquals(95000, $firstDaily->net_sales);

        $paymentMethods = $result['payment_methods'];
        $this->assertNotEmpty($paymentMethods);
        $firstPayment = $paymentMethods[0];

        $this->assertEquals('Cash', $firstPayment->payment_name);
        $this->assertEquals(1, $firstPayment->total_transactions);
        $this->assertEquals(95000, $firstPayment->total_revenue);
    }
}
