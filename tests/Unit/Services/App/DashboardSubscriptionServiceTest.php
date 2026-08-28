<?php

namespace Tests\Unit\Services\App;

use App\Services\App\DashboardSubscriptionService;
use Tests\TestCase;

class DashboardSubscriptionServiceTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $service = new DashboardSubscriptionService();
        $this->assertInstanceOf(DashboardSubscriptionService::class, $service);
    }
}
