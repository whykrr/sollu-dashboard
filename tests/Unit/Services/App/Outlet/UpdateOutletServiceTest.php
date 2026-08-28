<?php

namespace Tests\Unit\Services\App\Outlet;

use App\Models\Outlet;
use App\Models\User;
use App\Services\App\Outlet\UpdateOutletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateOutletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UpdateOutletService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateOutletService;
    }

    public function test_it_updates_outlet()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Old Outlet Name',
        ]);

        $data = [
            'name' => 'New Outlet Name',
            'phone' => '87654321',
        ];

        $result = $this->service->execute($outlet, $data, $user);

        $this->assertEquals('New Outlet Name', $result->name);
        $this->assertEquals('87654321', $result->phone);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'action' => 'updated',
        ]);
    }
}
