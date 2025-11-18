<x-mail::message>
# {{ __('Thank you for your purchase!') }}

{{ __('Hi :name,', ['name' => $sale->customer?->name ?? __('Customer')]) }}

{{ __('We appreciate your business. Your invoice #:number is attached to this email.', ['number' => str_pad($sale->id, 5, '0', STR_PAD_LEFT)]) }}

**{{ __('Order Summary') }}**

- {{ __('Date') }}: {{ $sale->date?->format('d M Y H:i') }}
- {{ __('Payment Method') }}: {{ ucfirst($sale->payment_type) }}
- {{ __('Total Amount') }}: {{ currency_format($sale->total_amount) }}

<x-mail::panel>
{{ __('Need help or have questions? Reply to this email or contact our support team anytime.') }}
</x-mail::panel>

{{ __('Warm regards,') }}<br>
{{ config('app.name') }}
</x-mail::message>
