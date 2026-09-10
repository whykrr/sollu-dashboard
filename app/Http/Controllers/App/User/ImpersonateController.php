<?php

namespace App\Http\Controllers\App\User;

use App\Constants\AuthorizationMessage;
use App\Constants\FlashDataVariable;
use App\Constants\ResourceMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ImpersonateController extends Controller
{
    /**
     * Authenticate the merchant user via one-time impersonation token.
     */
    public function authenticate(Request $request, string $token): RedirectResponse
    {
        /** @var array{user_id: string, business_id: string, admin_id: string, created_at: int}|null $payload */
        $payload = Cache::pull("impersonate:token:{$token}");

        if (! $payload || empty($payload['user_id']) || empty($payload['business_id'])) {
            return redirect()->route('login')->with(
                FlashDataVariable::FAILED->value,
                AuthorizationMessage::IMPERSONATE_INVALID
            );
        }

        /** @var User|null $user */
        $user = User::query()
            ->where('id', $payload['user_id'])
            ->where('business_id', $payload['business_id'])
            ->first();

        if (! $user) {
            return redirect()->route('login')->with(
                FlashDataVariable::FAILED->value,
                AuthorizationMessage::IMPERSONATE_INVALID
            );
        }

        Auth::guard('business')->login($user);

        $request->session()->regenerate();
        $request->session()->put('impersonated_by', $payload['admin_id']);

        return redirect()->route('overview')->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::IMPERSONATE_SUCCESS
        );
    }
}
