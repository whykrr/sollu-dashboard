<?php

namespace Tests\Unit\Services\App;

use App\Models\Master\Customer;
use App\Models\Outlet;
use App\Models\Sales\Transaction;
use App\Models\User;
use App\Services\App\Customer\CustomerService;
use App\Services\App\Master\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerService $service;

    protected $activityLogServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activityLogServiceMock = Mockery::mock(ActivityLogService::class);
        $this->service = new CustomerService($this->activityLogServiceMock);
    }

    public function test_it_gets_paginated_customers()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();

        Customer::create([
            'business_id' => $user->business_id,
            'name' => 'John Doe',
            'phone' => '08123456789',
            'email' => 'john@test.com',
            'is_active' => true,
        ]);

        Customer::create([
            'business_id' => $user->business_id,
            'name' => 'Jane Smith',
            'phone' => '08987654321',
            'email' => 'jane@test.com',
            'is_active' => false,
        ]);

        $filters = ['search' => 'John', 'is_active' => true];
        $result = $this->service->getPaginated($filters);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('John Doe', $result->items()[0]->name);
    }

    public function test_it_gets_summary_stats()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();

        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Main Outlet',
        ]);

        $customer = Customer::create([
            'business_id' => $user->business_id,
            'name' => 'John Doe',
            'phone' => '08123456789',
        ]);

        Transaction::create([
            'outlet_id' => $outlet->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'total' => 50000,
            'transaction_number' => 'TRX-001',
        ]);

        $stats = $this->service->getSummaryStats($customer);

        $this->assertEquals(1, $stats['total_transactions']);
        $this->assertEquals(50000, $stats['total_spent']);
        $this->assertEquals(50000, $stats['average_spent']);
        $this->assertCount(1, $stats['recent_transactions']);
    }

    public function test_it_creates_customer()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $this->actingAs($user);

        $data = [
            'business_id' => $user->business_id,
            'name' => 'New Customer',
            'phone' => '08000000000',
        ];

        $this->activityLogServiceMock
            ->shouldReceive('log')
            ->once()
            ->with(Mockery::type(Customer::class), 'created', $user);

        $customer = $this->service->create($data);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('New Customer', $customer->name);
        $this->assertDatabaseHas('customers', ['name' => 'New Customer']);
    }

    public function test_it_updates_customer()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $this->actingAs($user);

        $customer = Customer::create([
            'business_id' => $user->business_id,
            'name' => 'Old Name',
            'phone' => '08000000000',
        ]);

        $this->activityLogServiceMock
            ->shouldReceive('log')
            ->once()
            ->with(Mockery::type(Customer::class), 'updated', $user);

        $updated = $this->service->update($customer, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('customers', ['name' => 'New Name']);
    }

    public function test_it_soft_deletes_customer_with_transactions()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $this->actingAs($user);

        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Main Outlet',
        ]);

        $customer = Customer::create([
            'business_id' => $user->business_id,
            'name' => 'John Doe',
            'phone' => '08123456789',
        ]);

        Transaction::create([
            'outlet_id' => $outlet->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'total' => 50000,
            'transaction_number' => 'TRX-001',
        ]);

        $this->activityLogServiceMock
            ->shouldReceive('log')
            ->once()
            ->with(Mockery::type(Customer::class), 'deactivated (soft delete via update)', $user);

        $this->service->delete($customer);

        $customer->refresh();
        $this->assertFalse($customer->is_active);
    }

    public function test_it_hard_deletes_customer_without_transactions()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $this->actingAs($user);

        $customer = Customer::create([
            'business_id' => $user->business_id,
            'name' => 'John Doe',
            'phone' => '08123456789',
        ]);

        $this->activityLogServiceMock
            ->shouldReceive('log')
            ->once()
            ->with(Mockery::type(Customer::class), 'deleted', $user);

        $this->service->delete($customer);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_it_searches_active_customers()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();

        Customer::create([
            'business_id' => $user->business_id,
            'name' => 'John Active',
            'phone' => '08123456789',
            'is_active' => true,
        ]);

        Customer::create([
            'business_id' => $user->business_id,
            'name' => 'John Inactive',
            'phone' => '08987654321',
            'is_active' => false,
        ]);

        $results = $this->service->searchActive('John');

        $this->assertCount(1, $results);
        $this->assertEquals('John Active', $results[0]->name);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
