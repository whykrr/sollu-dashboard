<?php

namespace App\Http\Requests\Cockpit\SubscriptionManualPaymentMethod;

use App\Http\Requests\BaseInertiaFormRequest;

class UpdateSubscriptionManualPaymentMethodRequest extends BaseInertiaFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
