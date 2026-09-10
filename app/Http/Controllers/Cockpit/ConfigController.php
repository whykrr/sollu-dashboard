<?php

namespace App\Http\Controllers\Cockpit;

use App\Constants\FlashDataVariable;
use App\Constants\ResourceMessage;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigController extends Controller
{
    public function index()
    {
        $midtransEnabled = SystemSetting::isMidtransEnabled();
        $helpCenterUrl = SystemSetting::get('help_center_url', '');

        return Inertia::render('Cockpit/Config/Index', [
            'midtransEnabled' => $midtransEnabled,
            'settings' => [
                'help_center_url' => $helpCenterUrl,
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'help_center_url' => ['nullable', 'string', 'max:500'],
        ]);

        $url = $validated['help_center_url'] ?? null;
        if (! empty($url)) {
            $url = trim($url);
            if (! preg_match('~^(?:f|ht)tps?://~i', $url) && ! str_starts_with($url, '#') && ! str_starts_with($url, 'mailto:') && ! str_starts_with($url, 'tel:')) {
                $url = 'https://'.$url;
            }
        }

        SystemSetting::set('help_center_url', $url);

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::UPDATE_SUCCESS
        );
    }

    public function updateFlag(Request $request)
    {
        $validated = $request->validate([
            'feature_name' => 'required|string',
            'enabled' => 'required|boolean',
        ]);

        SystemSetting::set($validated['feature_name'], $validated['enabled'] ? '1' : '0', 'payment');

        return redirect()->back()->with(FlashDataVariable::SUCCESS->value, ResourceMessage::UPDATE_SUCCESS);
    }
}
