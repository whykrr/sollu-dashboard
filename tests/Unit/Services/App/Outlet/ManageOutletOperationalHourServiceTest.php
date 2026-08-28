<?php

namespace Tests\Unit\Services\App\Outlet;

use App\Models\Outlet;
use App\Models\User;
use App\Services\App\Outlet\ManageOutletOperationalHourService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageOutletOperationalHourServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ManageOutletOperationalHourService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ManageOutletOperationalHourService;
    }

    public function test_it_upserts_operational_hours()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Test',
        ]);

        // Pre-create Monday to simulate update
        $outlet->operationalHours()->create([
            'day_of_week' => 1,
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
            'is_closed' => false,
        ]);

        $hours = [
            [
                'day_of_week' => 1, // Monday
                'open_time' => '09:00:00',
                'close_time' => '18:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 2, // Tuesday
                'open_time' => null,
                'close_time' => null,
                'is_closed' => true,
            ],
        ];

        $result = $this->service->upsertHours($outlet, $hours, $user);

        $this->assertCount(2, $result);

        $this->assertDatabaseHas('outlet_operational_hours', [
            'outlet_id' => $outlet->id,
            'day_of_week' => 1,
            'open_time' => '09:00:00',
            'close_time' => '18:00:00',
            'is_closed' => false,
        ]);

        $this->assertDatabaseHas('outlet_operational_hours', [
            'outlet_id' => $outlet->id,
            'day_of_week' => 2,
            'is_closed' => true,
        ]);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'action' => 'operational_hours_updated',
        ]);
    }
}
