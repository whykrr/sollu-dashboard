<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessInfoController extends Controller
{
    /**
     * Retrieve optimized business summary info for internal UI components.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $businessId = $user->business_id;

        /** @var Business|null $business */
        $business = Business::query()
            ->where('id', $businessId)
            ->select(['id', 'business_type_id', 'trial_end_at'])
            ->with(['type:id,name'])
            ->first();

        if (! $business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        $subscription = $business->subscriptions()
            ->where('status', 'active')
            ->select(['id', 'business_id', 'plan_id', 'expired_at'])
            ->with(['plan:id,name'])
            ->latest('started_at')
            ->first();

        $isTrial = $business->trial_end_at ? Carbon::parse($business->trial_end_at)->isFuture() : false;

        if ($subscription) {
            $planName = $subscription->plan?->name ?? 'Default Plan';
            $expiredAt = $subscription->expired_at?->toISOString() ?? (string) $subscription->expired_at;
        } elseif ($isTrial) {
            $planName = 'Trial Plan';
            $expiredAt = $business->trial_end_at ? Carbon::parse($business->trial_end_at)->toISOString() : null;
        } else {
            $planName = 'Free Plan';
            $expiredAt = null;
        }

        $outletCount = Outlet::query()
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->count();

        return response()->json([
            'business_type' => $business->type?->name ?? 'Unknown',
            'plan_name' => $planName,
            'expired_at' => $expiredAt,
            'outlet_count' => (int) $outletCount,
        ]);
    }
}
