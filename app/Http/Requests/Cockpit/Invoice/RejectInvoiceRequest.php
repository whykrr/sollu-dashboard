<?php

namespace App\Http\Requests\Cockpit\Invoice;

use App\Http\Requests\BaseInertiaFormRequest;

class RejectInvoiceRequest extends BaseInertiaFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
}
