<?php

namespace App\Http\Controllers\Cockpit;

use App\Constants\FlashDataVariable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cockpit\SubscriptionManualPaymentMethod\StoreSubscriptionManualPaymentMethodRequest;
use App\Http\Requests\Cockpit\SubscriptionManualPaymentMethod\UpdateSubscriptionManualPaymentMethodRequest;
use App\Models\Master\SubscriptionManualPaymentMethod;
use Inertia\Inertia;

class SubscriptionManualPaymentMethodController extends Controller
{
    public function index()
    {
        $methods = SubscriptionManualPaymentMethod::orderBy('bank_name')->get();

        return Inertia::render('Cockpit/PaymentMethod/Index', [
            'methods' => $methods,
        ]);
    }

    public function store(StoreSubscriptionManualPaymentMethodRequest $request)
    {
        SubscriptionManualPaymentMethod::create($request->validated());

        return redirect()->route('cockpit.payment-methods.index')
            ->with(FlashDataVariable::SUCCESS->value, 'Metode pembayaran berhasil ditambahkan.');
    }

    public function update(UpdateSubscriptionManualPaymentMethodRequest $request, string $id)
    {
        $method = SubscriptionManualPaymentMethod::findOrFail($id);
        $method->update($request->validated());

        return redirect()->route('cockpit.payment-methods.index')
            ->with(FlashDataVariable::SUCCESS->value, 'Metode pembayaran berhasil diperbarui.');
    }

    public function toggleStatus(string $id)
    {
        $method = SubscriptionManualPaymentMethod::findOrFail($id);
        $method->update(['is_active' => ! $method->is_active]);

        return redirect()->back()
            ->with(FlashDataVariable::SUCCESS->value, 'Status metode pembayaran berhasil diubah.');
    }

    public function destroy(string $id)
    {
        $method = SubscriptionManualPaymentMethod::findOrFail($id);
        $method->delete();

        return redirect()->back()
            ->with(FlashDataVariable::SUCCESS->value, 'Metode pembayaran berhasil dihapus.');
    }
}
