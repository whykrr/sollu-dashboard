<?php

namespace Tests\Unit\Services\App\Inventory;

use App\Models\Outlet;
use App\Models\User;
use App\Services\App\Inventory\StockFreezeService;
use App\Services\Shared\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class StockFreezeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityLogService $activityLogServiceMock;
    protected StockFreezeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->activityLogServiceMock = Mockery::mock(ActivityLogService::class);
        $this->activityLogServiceMock->shouldReceive('log')->andReturnNull();

        $this->service = new StockFreezeService($this->activityLogServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function setupBaseData()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $business = $user->business;
        $outlet = clone $business->outlets()->first(); // clone to avoid cache
        
        return [$user, $business, $outlet];
    }

    public function test_it_freezes_stock()
    {
        [$user, $business, $outlet] = $this->setupBaseData();
        
        $outlet->is_stock_frozen = false;
        $outlet->save();

        $frozenOutlet = $this->service->freeze($outlet, $user);

        $this->assertTrue($frozenOutlet->is_stock_frozen);
        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'is_stock_frozen' => true,
        ]);
    }

    public function test_it_unfreezes_stock()
    {
        [$user, $business, $outlet] = $this->setupBaseData();
        
        $outlet->is_stock_frozen = true;
        $outlet->save();

        $unfrozenOutlet = $this->service->unfreeze($outlet, $user);

        $this->assertFalse($unfrozenOutlet->is_stock_frozen);
        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'is_stock_frozen' => false,
        ]);
    }

    public function test_it_asserts_not_frozen_successfully()
    {
        [$user, $business, $outlet] = $this->setupBaseData();
        
        $outlet->is_stock_frozen = false;
        $outlet->save();

        $this->expectNotToPerformAssertions();
        
        $this->service->assertNotFrozen($outlet);
    }

    public function test_it_throws_exception_when_asserting_frozen_stock()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('sedang dibekukan');

        [$user, $business, $outlet] = $this->setupBaseData();
        
        $outlet->is_stock_frozen = true;
        $outlet->save();

        $this->service->assertNotFrozen($outlet);
    }
}
