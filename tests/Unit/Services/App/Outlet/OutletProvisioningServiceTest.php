<?php

namespace Tests\Unit\Services\App\Outlet;

use App\Models\Master\OutletPaymentMethod;
use App\Models\Master\PaymentMethod;
use App\Models\Outlet;
use App\Models\OutletOperationalHour;
use App\Models\OutletSetting;
use App\Models\User;
use App\Services\App\Outlet\OutletProvisioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletProvisioningServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OutletProvisioningService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OutletProvisioningService;
    }

    public function test_it_provisions_all()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Provisioning',
        ]);

        $this->service->provisionAll($outlet);

        // Check Payment Methods
        $this->assertDatabaseHas('payment_methods', [
            'business_id' => $outlet->business_id,
            'type' => 'cash',
        ]);
        $this->assertDatabaseHas('payment_methods', [
            'business_id' => $outlet->business_id,
            'type' => 'qris',
        ]);

        $paymentMethodIds = PaymentMethod::where('business_id', $outlet->business_id)->pluck('id')->toArray();
        $outletPaymentMethods = OutletPaymentMethod::where('outlet_id', $outlet->id)->count();
        $this->assertEquals(2, $outletPaymentMethods);

        // Check Receipt Settings
        $layoutConfigSetting = OutletSetting::where('outlet_id', $outlet->id)
            ->where('category', 'receipt')
            ->where('key', 'layout_config')
            ->first();
        $this->assertNotNull($layoutConfigSetting);
        $this->assertIsArray($layoutConfigSetting->value);
        $this->assertEquals('58mm', $layoutConfigSetting->value['paper_size']);

        $autoPrintSetting = OutletSetting::where('outlet_id', $outlet->id)
            ->where('category', 'pos')
            ->where('key', 'auto_print')
            ->first();
        $this->assertEquals(1, $autoPrintSetting->value);

        // Check Financial Settings
        $taxSetting = OutletSetting::where('outlet_id', $outlet->id)
            ->where('category', 'financial')
            ->where('key', 'tax')
            ->first();
        $this->assertEquals(0.0, $taxSetting->value);

        // Check Operational Hours
        $hours = OutletOperationalHour::where('outlet_id', $outlet->id)->get();
        $this->assertCount(7, $hours);
        $this->assertEquals('08:00', $hours->first()->open_time);
        $this->assertEquals('22:00', $hours->first()->close_time);
    }
}
