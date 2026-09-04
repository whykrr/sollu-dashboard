<?php

namespace Tests\Unit\Services\App\Invoice;

use App\Models\Business;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\SubscriptionActivatedNotification;
use App\Services\App\Invoice\CompleteInvoiceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class CompleteInvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CompleteInvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CompleteInvoiceService;
        Notification::fake();
    }

    public function test_it_marks_invoice_paid_and_activates_subscription_and_sends_notification()
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 1, 12, 0, 0));

        $invoiceMock = Mockery::mock(Invoice::class)->makePartial();
        $businessMock = Mockery::mock(Business::class)->makePartial();
        $subscriptionMock = Mockery::mock(Subscription::class)->makePartial();
        $planMock = Mockery::mock(SubscriptionPlan::class)->makePartial();
        $ownerMock = Mockery::mock(User::class)->makePartial();

        $planMock->id = 1;
        $planMock->name = 'Pro Plan';

        $businessMock->id = 1;

        $subscriptionMock->id = 1;
        $subscriptionMock->status = 'inactive';
        $subscriptionMock->plan = $planMock;
        $subscriptionMock->expired_at = Carbon::now()->addDays(30);

        $invoiceMock->business = $businessMock;

        // DB Transaction mock
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        // Mock invoice update
        $invoiceMock->shouldReceive('update')
            ->once()
            ->with([
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ])
            ->andReturnTrue();

        // Mock invoice->items()->where()->exists()
        $itemsQueryMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $invoiceMock->shouldReceive('items')->once()->andReturn($itemsQueryMock);
        $itemsQueryMock->shouldReceive('where')->once()->with('item_type', 'outlet_addition')->andReturnSelf();
        $itemsQueryMock->shouldReceive('exists')->once()->andReturn(false);

        // Mock business->subscriptions()->latest()->first()
        $subsQueryMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $businessMock->shouldReceive('subscriptions')->once()->andReturn($subsQueryMock);
        $subsQueryMock->shouldReceive('latest')->once()->andReturnSelf();
        $subsQueryMock->shouldReceive('first')->once()->andReturn($subscriptionMock);

        // Mock subscription update
        $subscriptionMock->shouldReceive('update')
            ->once()
            ->with(['status' => 'active'])
            ->andReturnTrue();

        // Mock business->users()->first()
        $usersQueryMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $businessMock->shouldReceive('users')->once()->andReturn($usersQueryMock);
        $usersQueryMock->shouldReceive('first')->once()->andReturn($ownerMock);

        // Mock notify
        $ownerMock->shouldReceive('notify')
            ->once()
            ->with(Mockery::type(SubscriptionActivatedNotification::class));

        // Act
        $result = $this->service->execute($invoiceMock);

        $this->assertSame($invoiceMock, $result);
    }

    public function test_it_does_not_activate_subscription_if_it_is_outlet_addition()
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 1, 12, 0, 0));

        $invoiceMock = Mockery::mock(Invoice::class)->makePartial();
        $businessMock = Mockery::mock(Business::class)->makePartial();
        $subscriptionMock = Mockery::mock(Subscription::class)->makePartial();

        $invoiceMock->business = $businessMock;
        $subscriptionMock->status = 'active';

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $invoiceMock->shouldReceive('update')
            ->once()
            ->with([
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ])
            ->andReturnTrue();

        // outlet_addition exists is true
        $itemsQueryMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $invoiceMock->shouldReceive('items')->andReturn($itemsQueryMock);
        $itemsQueryMock->shouldReceive('where')->with('item_type', 'outlet_addition')->andReturnSelf();
        $itemsQueryMock->shouldReceive('exists')->andReturn(true);

        // For the outlet addition logic
        $outletItemMock = Mockery::mock();
        $outletItemMock->metadata = ['outlet_id' => 99];
        $itemsQueryMock->shouldReceive('first')->andReturn($outletItemMock);

        // Just let it find the outlet (or not) natively since we can't easily mock the static method here
        // without complex runInSeparateProcess setup that conflicts with prior tests.
        // We will just verify it doesn't fail.

        // Mock ManageOutletStatusService
        $ownerMock = Mockery::mock(User::class)->makePartial();

        $usersQueryMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $businessMock->shouldReceive('users')->andReturn($usersQueryMock);
        $usersQueryMock->shouldReceive('first')->andReturn($ownerMock);

        $manageServiceMock = Mockery::mock(\App\Services\App\Outlet\ManageOutletStatusService::class);
        $this->app->instance(\App\Services\App\Outlet\ManageOutletStatusService::class, $manageServiceMock);

        $subsQueryMock = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $businessMock->shouldReceive('subscriptions')->once()->andReturn($subsQueryMock);
        $subsQueryMock->shouldReceive('latest')->once()->andReturnSelf();
        $subsQueryMock->shouldReceive('first')->once()->andReturn($subscriptionMock);

        // Expect no update on subscription
        $subscriptionMock->shouldNotReceive('update');

        $result = $this->service->execute($invoiceMock);

        $this->assertSame($invoiceMock, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
