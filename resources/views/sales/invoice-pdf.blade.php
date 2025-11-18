<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header, .footer { text-align: center; }
        .header h1 { margin-bottom: 0; }
        .info { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f9fafb; text-transform: uppercase; font-size: 11px; color: #374151; }
        .totals { margin-top: 20px; width: 40%; float: right; }
        .totals table { border: none; }
        .totals td { border: none; padding: 6px 8px; }
        .totals tr.total td { font-weight: bold; border-top: 1px solid #111; }
    </style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'BillingPOS') }}</h1>
    <p>{{ __('Invoice #:number', ['number' => str_pad($sale->id, 5, '0', STR_PAD_LEFT)]) }}</p>
</div>

<div class="info">
    <table>
        <tr>
            <td width="50%">
                <strong>{{ __('Billed To') }}:</strong><br>
                {{ $sale->customer?->name ?? __('Walk-in Customer') }}<br>
                {{ $sale->customer?->email }}<br>
                {{ $sale->customer?->phone }}<br>
                {{ $sale->customer?->address }}
            </td>
            <td width="50%">
                <strong>{{ __('Invoice Date') }}:</strong> {{ $sale->date?->format('d M Y H:i') }}<br>
                <strong>{{ __('Payment Method') }}:</strong> {{ ucfirst($sale->payment_type) }}<br>
                <strong>{{ __('Currency') }}:</strong> {{ config('app.currency', 'INR') }}
            </td>
        </tr>
    </table>
</div>

<table>
    <thead>
    <tr>
        <th>{{ __('Item') }}</th>
        <th style="text-align:right;">{{ __('Qty') }}</th>
        <th style="text-align:right;">{{ __('Price') }}</th>
        <th style="text-align:right;">{{ __('Total') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->product?->name ?? __('Product') }}</td>
            <td style="text-align:right;">{{ $item->quantity }}</td>
            <td style="text-align:right;">{{ currency_format($item->price) }}</td>
            <td style="text-align:right;">{{ currency_format($item->total) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td>{{ __('Subtotal') }}</td>
            <td style="text-align:right;">{{ currency_format($sale->items->sum('total')) }}</td>
        </tr>
        <tr>
            <td>{{ __('Discount') }}</td>
            <td style="text-align:right;">{{ currency_format($sale->discount) }}</td>
        </tr>
        <tr>
            <td>{{ __('Tax') }}</td>
            <td style="text-align:right;">{{ currency_format($sale->tax) }}</td>
        </tr>
        <tr class="total">
            <td>{{ __('Grand Total') }}</td>
            <td style="text-align:right;">{{ currency_format($sale->total_amount) }}</td>
        </tr>
    </table>
</div>

<div style="clear: both;"></div>

<div class="footer">
    <p>{{ __('Thank you for your business!') }}</p>
</div>
</body>
</html>

