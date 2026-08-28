<?php

namespace Tests\Unit\Services\App\Reports;

use App\Models\Outlet;
use App\Models\Promo;
use App\Models\Sales\Transaction;
use App\Models\Sales\TransactionPromo;
use App\Models\User;
use App\Services\App\Reports\PromotionReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PromotionReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PromotionReportService;
    }

    public function test_it_gets_report()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Report',
        ]);

        $promo = Promo::create([
            'business_id' => $user->business_id,
            'name' => 'Diskon Merdeka',
            'promo_type' => 'fixed',
            'target_type' => 'bill',
            'discount_value' => 10000,
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(10),
            'created_by' => $user->id,
        ]);

        $now = Carbon::now();

        $transaction = Transaction::create([
            'outlet_id' => $outlet->id,
            'status' => 'completed',
            'subtotal' => 100000,
            'total' => 90000,
            'transaction_number' => 'TRX-101',
            'created_at' => $now,
        ]);

        TransactionPromo::create([
            'transaction_id' => $transaction->id,
            'promo_id' => $promo->id,
            'promo_name' => 'Diskon Merdeka',
            'discount_type' => 'fixed',
            'discount_amount' => 10000,
            'discount_value' => 10000,
        ]);

        $startDate = $now->copy()->subDay();
        $endDate = $now->copy()->addDay();

        $result = $this->service->getReport($outlet->id, $startDate, $endDate);

        $this->assertNotEmpty($result->items());
        $firstItem = $result->items()[0];

        $this->assertEquals('Diskon Merdeka', $firstItem->promo_name);
        $this->assertEquals('fixed', $firstItem->promo_type);
        $this->assertEquals(1, $firstItem->total_usage);
        $this->assertEquals(10000, $firstItem->total_discount_given);
    }
}
