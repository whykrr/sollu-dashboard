<?php

namespace Tests\Unit\Services\App\Reports;

use App\Models\Master\Customer;
use App\Models\Outlet;
use App\Models\Sales\Transaction;
use App\Models\User;
use App\Services\App\Reports\CustomerReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CustomerReportService;
    }

    public function test_it_gets_report()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Report',
        ]);

        $customer = Customer::create([
            'business_id' => $user->business_id,
            'name' => 'John Doe',
            'phone' => '08123456789',
        ]);

        Transaction::create([
            'outlet_id' => $outlet->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'total' => 50000,
            'transaction_number' => 'TRX-001',
        ]);

        Transaction::create([
            'outlet_id' => $outlet->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'total' => 75000,
            'transaction_number' => 'TRX-002',
        ]);

        $startDate = Carbon::now()->subDays(1);
        $endDate = Carbon::now()->addDays(1);

        $result = $this->service->getReport($outlet->id, $startDate, $endDate);

        $this->assertNotEmpty($result->items());
        $firstItem = $result->items()[0];

        $this->assertEquals($customer->id, $firstItem->id);
        $this->assertEquals('John Doe', $firstItem->name);
        $this->assertEquals(2, $firstItem->total_visits);
        $this->assertEquals(125000, $firstItem->total_spent);
    }
}
