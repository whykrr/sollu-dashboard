<?php

namespace Tests\Feature\Auth;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\CockpitUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_guard_login_with_remember_creates_30_day_cookie(): void
    {
        $appHost = config('domain.app', 'app.sollu.test');

        $type = BusinessType::create([
            'name' => 'F&B',
            'code' => 'fnb_remember',
            'is_visible' => true,
        ]);

        $business = Business::create([
            'name' => 'Merchant Remember Test',
            'owner_name' => 'Test Owner',
            'email' => 'merchant_remember@sollu.test',
            'phone' => '081234567801',
            'business_type_id' => $type->id,
            'status' => 'active',
            'trial_end_at' => now()->addDays(14),
        ]);

        $merchantUser = User::create([
            'business_id' => $business->id,
            'name' => 'Merchant Owner',
            'email' => 'merchant_rem@sollu.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->withServerVariables(['HTTP_HOST' => $appHost])
            ->post("http://{$appHost}/login", [
                'email' => 'merchant_rem@sollu.test',
                'password' => 'password123',
                'remember' => true,
            ]);

        $response->assertRedirect(route('overview'));
        $this->assertTrue(Auth::guard('business')->check());

        // Remember token must be populated
        $merchantUser->refresh();
        $this->assertNotEmpty($merchantUser->getRememberToken());

        // Recaller cookie must exist and expire in ~30 days (43200 minutes = 2592000 seconds)
        $recallerName = Auth::guard('business')->getRecallerName();
        $response->assertCookie($recallerName);

        $cookie = $response->getCookie($recallerName);
        $this->assertNotNull($cookie);
        $remainingSeconds = $cookie->getExpiresTime() - time();
        $expectedSeconds = 30 * 24 * 60 * 60; // 2,592,000s
        $this->assertEqualsWithDelta($expectedSeconds, $remainingSeconds, 30);
    }

    public function test_business_guard_login_without_remember_does_not_create_remember_cookie(): void
    {
        $appHost = config('domain.app', 'app.sollu.test');

        $type = BusinessType::create([
            'name' => 'F&B',
            'code' => 'fnb_no_remember',
            'is_visible' => true,
        ]);

        $business = Business::create([
            'name' => 'Merchant No Remember Test',
            'owner_name' => 'Test Owner',
            'email' => 'merchant_no_rem@sollu.test',
            'phone' => '081234567802',
            'business_type_id' => $type->id,
            'status' => 'active',
            'trial_end_at' => now()->addDays(14),
        ]);

        User::create([
            'business_id' => $business->id,
            'name' => 'Merchant Owner 2',
            'email' => 'merchant_no_rem@sollu.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->withServerVariables(['HTTP_HOST' => $appHost])
            ->post("http://{$appHost}/login", [
                'email' => 'merchant_no_rem@sollu.test',
                'password' => 'password123',
                'remember' => false,
            ]);

        $response->assertRedirect(route('overview'));
        $this->assertTrue(Auth::guard('business')->check());

        $recallerName = Auth::guard('business')->getRecallerName();
        $this->assertNull($response->getCookie($recallerName));
    }

    public function test_cockpit_guard_login_with_remember_creates_30_day_cookie_and_saves_token(): void
    {
        $cockpitHost = config('domain.cockpit', 'cockpit.sollu.test');

        $cockpitUser = CockpitUser::create([
            'name' => 'Cockpit Admin Remember',
            'email' => 'admin_remember@sollu.test',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->withServerVariables(['HTTP_HOST' => $cockpitHost])
            ->post("http://{$cockpitHost}/login", [
                'email' => 'admin_remember@sollu.test',
                'password' => 'password123',
                'remember' => true,
            ]);

        $response->assertRedirect(route('cockpit.dashboard'));
        $this->assertTrue(Auth::guard('cockpit')->check());

        // Remember token must be saved to DB without database column exception
        $cockpitUser->refresh();
        $this->assertNotEmpty($cockpitUser->getRememberToken());

        // Recaller cookie must exist and expire in ~30 days (43200 minutes = 2592000 seconds)
        $recallerName = Auth::guard('cockpit')->getRecallerName();
        $response->assertCookie($recallerName);

        $cookie = $response->getCookie($recallerName);
        $this->assertNotNull($cookie);
        $remainingSeconds = $cookie->getExpiresTime() - time();
        $expectedSeconds = 30 * 24 * 60 * 60; // 2,592,000s
        $this->assertEqualsWithDelta($expectedSeconds, $remainingSeconds, 30);
    }

    public function test_cockpit_guard_login_without_remember_does_not_create_remember_cookie(): void
    {
        $cockpitHost = config('domain.cockpit', 'cockpit.sollu.test');

        CockpitUser::create([
            'name' => 'Cockpit Admin No Remember',
            'email' => 'admin_no_rem@sollu.test',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->withServerVariables(['HTTP_HOST' => $cockpitHost])
            ->post("http://{$cockpitHost}/login", [
                'email' => 'admin_no_rem@sollu.test',
                'password' => 'password123',
                'remember' => false,
            ]);

        $response->assertRedirect(route('cockpit.dashboard'));
        $this->assertTrue(Auth::guard('cockpit')->check());

        $recallerName = Auth::guard('cockpit')->getRecallerName();
        $this->assertNull($response->getCookie($recallerName));
    }

    public function test_business_guard_transparent_reauthentication_via_recaller_cookie(): void
    {
        $appHost = config('domain.app', 'app.sollu.test');

        $type = BusinessType::create([
            'name' => 'F&B',
            'code' => 'fnb_reauth',
            'is_visible' => true,
        ]);

        $business = Business::create([
            'name' => 'Merchant Reauth Test',
            'owner_name' => 'Test Owner',
            'email' => 'merchant_reauth@sollu.test',
            'phone' => '081234567803',
            'business_type_id' => $type->id,
            'status' => 'active',
            'trial_end_at' => now()->addDays(14),
        ]);

        $merchantUser = User::create([
            'business_id' => $business->id,
            'name' => 'Merchant Owner 3',
            'email' => 'merchant_reauth@sollu.test',
            'password' => bcrypt('password123'),
        ]);

        // Login with remember: true
        $loginResponse = $this->withServerVariables(['HTTP_HOST' => $appHost])
            ->post("http://{$appHost}/login", [
                'email' => 'merchant_reauth@sollu.test',
                'password' => 'password123',
                'remember' => true,
            ]);

        $recallerName = Auth::guard('business')->getRecallerName();
        $recallerCookie = $loginResponse->getCookie($recallerName);
        $this->assertNotNull($recallerCookie);

        // Reset Auth and simulate fresh request without session but with recaller cookie
        Auth::forgetGuards();

        $reauthResponse = $this->withServerVariables(['HTTP_HOST' => $appHost])
            ->withUnencryptedCookies([$recallerName => $recallerCookie->getValue()])
            ->get("http://{$appHost}/");

        $this->assertTrue(Auth::guard('business')->check());
        $this->assertSame($merchantUser->id, Auth::guard('business')->id());
    }

    public function test_cockpit_guard_transparent_reauthentication_via_recaller_cookie(): void
    {
        $cockpitHost = config('domain.cockpit', 'cockpit.sollu.test');

        $cockpitUser = CockpitUser::create([
            'name' => 'Cockpit Admin Reauth',
            'email' => 'admin_reauth@sollu.test',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        // Login with remember: true
        $loginResponse = $this->withServerVariables(['HTTP_HOST' => $cockpitHost])
            ->post("http://{$cockpitHost}/login", [
                'email' => 'admin_reauth@sollu.test',
                'password' => 'password123',
                'remember' => true,
            ]);

        $recallerName = Auth::guard('cockpit')->getRecallerName();
        $recallerCookie = $loginResponse->getCookie($recallerName);
        $this->assertNotNull($recallerCookie);

        // Reset Auth and simulate fresh request without session but with recaller cookie
        Auth::forgetGuards();

        $reauthResponse = $this->withServerVariables(['HTTP_HOST' => $cockpitHost])
            ->withUnencryptedCookies([$recallerName => $recallerCookie->getValue()])
            ->get("http://{$cockpitHost}/dashboard");

        $this->assertTrue(Auth::guard('cockpit')->check());
        $this->assertSame($cockpitUser->id, Auth::guard('cockpit')->id());
    }
}
