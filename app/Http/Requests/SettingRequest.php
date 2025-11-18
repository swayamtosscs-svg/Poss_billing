<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->can('is-admin') || $user?->can('is-manager');
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'store_address' => ['nullable', 'string', 'max:500'],
            'store_tax_id' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_cropped' => ['nullable', 'string'],
            'remove_logo' => ['nullable', 'boolean'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['string', 'max:50'],
            'locale' => ['required', 'in:en,hi'],
        ];
    }
}
