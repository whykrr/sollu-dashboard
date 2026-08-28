<?php

namespace Tests\Unit\Services\App\Outlet;

use App\Models\Outlet;
use App\Models\User;
use App\Services\App\Outlet\ManageOutletSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageOutletSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ManageOutletSettingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ManageOutletSettingService;
    }

    public function test_it_upserts_settings()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Test',
        ]);

        $outlet->settings()->create([
            'category' => 'general',
            'key' => 'receipt_footer',
            'value' => 'Thank you',
        ]);

        $settings = [
            [
                'category' => 'general',
                'key' => 'receipt_footer',
                'value' => 'Thanks for coming',
            ],
            [
                'category' => 'general',
                'key' => 'tax_rate',
                'value' => '10',
            ],
        ];

        $result = $this->service->upsertSettings($outlet, $settings, $user);

        $this->assertCount(2, $result);

        $this->assertDatabaseHas('outlet_settings', [
            'outlet_id' => $outlet->id,
            'category' => 'general',
            'key' => 'receipt_footer',
            'value' => '"Thanks for coming"',
        ]);

        $this->assertDatabaseHas('outlet_settings', [
            'outlet_id' => $outlet->id,
            'category' => 'general',
            'key' => 'tax_rate',
            'value' => '"10"',
        ]);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'action' => 'settings_updated',
        ]);
    }
}
