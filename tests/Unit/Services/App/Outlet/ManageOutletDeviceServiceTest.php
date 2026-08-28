<?php

namespace Tests\Unit\Services\App\Outlet;

use App\Models\Outlet;
use App\Models\OutletDevice;
use App\Models\User;
use App\Services\App\Outlet\ManageOutletDeviceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ManageOutletDeviceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ManageOutletDeviceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ManageOutletDeviceService;
    }

    public function test_it_creates_device()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Test',
        ]);

        $data = [
            'device_name' => 'POS 1',
            'device_type' => 'pos',
            'serial_number' => 'SN-12345',
            'is_active' => true,
        ];

        $device = $this->service->createDevice($outlet, $data, $user);

        $this->assertInstanceOf(OutletDevice::class, $device);
        $this->assertEquals('POS 1', $device->device_name);
        $this->assertEquals('pos', $device->device_type);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'action' => 'device_added',
        ]);
    }

    public function test_it_updates_device()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Test',
        ]);

        $device = OutletDevice::create([
            'outlet_id' => $outlet->id,
            'device_name' => 'POS Old',
            'device_type' => 'pos',
        ]);

        $data = [
            'device_name' => 'POS New',
            'device_type' => 'kds',
        ];

        $updatedDevice = $this->service->updateDevice($device, $data, $user);

        $this->assertEquals('POS New', $updatedDevice->device_name);
        $this->assertEquals('kds', $updatedDevice->device_type);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'action' => 'device_updated',
        ]);
    }

    public function test_it_deletes_device()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Test',
        ]);

        $device = OutletDevice::create([
            'outlet_id' => $outlet->id,
            'device_name' => 'Delete Me',
            'device_type' => 'pos',
        ]);

        Cache::shouldReceive('forget')->once()->with("pos_device_{$device->id}");

        $this->service->deleteDevice($device, $user);

        $this->assertDatabaseMissing('outlet_devices', [
            'id' => $device->id,
        ]);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'action' => 'device_deleted',
        ]);
    }
}
