<?php

namespace Tests\Unit\Services\App\Reports;

use App\Models\Outlet;
use App\Models\Sales\Shift;
use App\Models\User;
use App\Services\App\Reports\CashierShiftReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierShiftReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CashierShiftReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CashierShiftReportService();
    }

    public function test_it_gets_report()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Report',
        ]);

        $shift = Shift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_number' => 'SHIFT-001',
            'status' => 'closed',
            'opening_cash' => 100000,
            'expected_cash' => 150000,
            'closing_cash' => 145000,
            'closed_at' => now(),
        ]);

        $startDate = Carbon::now()->subDays(1);
        $endDate = Carbon::now()->addDays(1);

        $result = $this->service->getReport($outlet->id, $startDate, $endDate);

        $this->assertNotEmpty($result->items());
        $firstItem = $result->items()[0];

        $this->assertEquals($shift->id, $firstItem->id);
        $this->assertEquals($user->name, $firstItem->cashier_name);
        $this->assertEquals(100000, $firstItem->starting_cash);
        $this->assertEquals(150000, $firstItem->expected_ending_cash);
        $this->assertEquals(145000, $firstItem->actual_ending_cash);
        $this->assertEquals(-5000, $firstItem->difference);
    }
}
