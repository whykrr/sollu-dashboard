<?php

namespace App\Http\Controllers\Cockpit;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigController extends Controller
{
    public function index()
    {
        $midtransEnabled = FeatureFlag::isMidtransEnabled();

        return Inertia::render('Cockpit/Config/Index', [
            'midtransEnabled' => $midtransEnabled,
        ]);
    }

    public function updateFlag(Request $request)
    {
        $validated = $request->validate([
            'feature_name' => 'required|string',
            'enabled' => 'required|boolean',
        ]);

        FeatureFlag::updateOrCreate(
            ['business_id' => null, 'feature_name' => $validated['feature_name']],
            ['enabled' => $validated['enabled']]
        );

        return redirect()->back()->with(\App\Constants\FlashDataVariable::SUCCESS->value, \App\Constants\ResourceMessage::UPDATE_SUCCESS);
    }
}
