<?php

namespace Tests\Unit\Services\App\Inventory;

use App\Models\Business;
use App\Models\Inventory\InventoryBalance;
use App\Models\Inventory\InventoryItem;
use App\Models\Outlet;
use App\Models\User;
use App\Services\App\Inventory\RawMaterialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RawMaterialServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RawMaterialService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RawMaterialService();
    }

    private function setupBaseData()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $business = $user->business;
        
        // Ensure there is at least one active outlet
        $outlet = $business->outlets()->first();
        if (!$outlet) {
            $outlet = Outlet::create(['business_id' => $business->id, 'name' => 'Outlet Test', 'is_active' => true]);
        } else {
            $outlet->update(['is_active' => true]);
        }
        
        // Let's create another inactive outlet to ensure it doesn't get a balance
        Outlet::create(['business_id' => $business->id, 'name' => 'Inactive Outlet', 'is_active' => false]);

        return [$user, $business, $outlet];
    }

    public function test_it_creates_raw_material_and_initializes_balances()
    {
        // Arrange
        [$user, $business, $outlet] = $this->setupBaseData();
        
        $data = [
            'name' => 'Flour',
            'sku' => 'FL-001',
            'uom_id' => null,
            'is_track_stock' => true,
        ];

        // Act
        $item = $this->service->createRawMaterial($data, $business);

        // Assert
        $this->assertInstanceOf(InventoryItem::class, $item);
        $this->assertEquals('Flour', $item->name);
        $this->assertEquals('raw_material', $item->item_type);

        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'name' => 'Flour',
            'item_type' => 'raw_material',
        ]);

        // Check balances initialized for active outlets only
        $activeOutletsCount = $business->outlets()->active()->count();
        $balancesCount = InventoryBalance::where('inventory_item_id', $item->id)->count();
        
        $this->assertEquals($activeOutletsCount, $balancesCount);
        $this->assertGreaterThan(0, $activeOutletsCount);
        
        $this->assertDatabaseHas('inventory_balances', [
            'inventory_item_id' => $item->id,
            'outlet_id' => $outlet->id,
            'current_stock' => 0,
        ]);
    }

    public function test_it_updates_raw_material()
    {
        // Arrange
        [$user, $business, $outlet] = $this->setupBaseData();
        $item = $this->service->createRawMaterial([
            'name' => 'Sugar',
            'sku' => 'SG-001',
        ], $business);

        // Act
        $updatedItem = $this->service->updateRawMaterial($item, [
            'name' => 'Brown Sugar',
            'sku' => 'BSG-001',
        ]);

        // Assert
        $this->assertEquals('Brown Sugar', $updatedItem->name);
        $this->assertEquals('BSG-001', $updatedItem->sku);
        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'name' => 'Brown Sugar',
            'sku' => 'BSG-001',
        ]);
    }
}
