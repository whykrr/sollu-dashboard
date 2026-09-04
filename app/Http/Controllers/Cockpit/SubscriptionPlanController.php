<?php

namespace App\Http\Controllers\Cockpit;

use App\Constants\FlashDataVariable;
use App\Constants\ResourceMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cockpit\UpdateSubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionPlanController extends Controller
{
    public function index(): Response
    {
        $plans = SubscriptionPlan::query()
            ->withCount(['subscriptions' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('price_per_outlet', 'asc')
            ->get();

        return Inertia::render('Cockpit/SubscriptionPlan/Index', [
            'plans' => $plans,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $plan = SubscriptionPlan::query()
            ->withCount(['subscriptions' => function ($query) {
                $query->where('status', 'active');
            }])
            ->findOrFail($id);

        return response()->json($plan);
    }

    public function update(UpdateSubscriptionPlanRequest $request, string $id): RedirectResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $plan->update($request->validated());

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::UPDATE_SUCCESS
        );
    }

    public function toggleStatus(Request $request, string $id): RedirectResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $plan->update([
            'is_active' => ! $plan->is_active,
        ]);

        $statusText = $plan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            "Paket langganan {$plan->name} berhasil {$statusText}."
        );
    }
}
