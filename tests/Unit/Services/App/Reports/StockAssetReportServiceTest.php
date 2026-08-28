<?php

namespace Tests\Unit\Services\App\Reports;

use App\Models\Inventory\InventoryMovement;
use App\Models\Master\InventoryItem;
use App\Models\Outlet;
use App\Models\User;
use App\Services\App\Reports\StockAssetReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StockAssetReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockAssetReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StockAssetReportService();
    }

    public function test_it_gets_report()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Report',
        ]);

        $invItem = new InventoryItem([
            'business_id' => $user->business_id,
            'name' => 'Beras',
            'item_type' => 'raw_material',
        ]);
        $invItem->minimum_stock = 10;
        $invItem->save();

        $now = Carbon::now();

        DB::table('inventory_balances')->insert([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'business_id' => $user->business_id,
            'outlet_id' => $outlet->id,
            'inventory_item_id' => $invItem->id,
            'current_stock' => 15,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Stock in
        InventoryMovement::create([
            'business_id' => $user->business_id,
            'outlet_id' => $outlet->id,
            'inventory_item_id' => $invItem->id,
            'movement_type' => 'purchase',
            'qty_change' => 20,
            'stock_before' => 0,
            'stock_after' => 20,
            'created_at' => $now,
        ]);

        // Stock out
        InventoryMovement::create([
            'business_id' => $user->business_id,
            'outlet_id' => $outlet->id,
            'inventory_item_id' => $invItem->id,
            'movement_type' => 'sale',
            'qty_change' => -5,
            'stock_before' => 20,
            'stock_after' => 15,
            'created_at' => $now->copy()->addMinutes(5),
        ]);

        $startDate = $now->copy()->startOfDay();
        $endDate = $now->copy()->endOfDay();

        $result = $this->service->getReport($outlet->id, $startDate, $endDate);

        $this->assertNotEmpty($result->items());
        $firstItem = $result->items()[0];

        $this->assertEquals('Beras', $firstItem->item_name);
        $this->assertEquals(0, $firstItem->starting_stock);
        $this->assertEquals(20, $firstItem->stock_in);
        $this->assertEquals(5, $firstItem->stock_out);
        $this->assertEquals(15, $firstItem->closing_stock);
    }
}
