<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f9fafb; text-transform: uppercase; font-size: 11px; color: #374151; }
        .summary { margin: 15px 0; }
        .summary div { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h1>{{ __('Sales Report') }}</h1>
    <p>{{ __('Period: :from to :to', ['from' => $from->format('d M Y'), 'to' => $to->format('d M Y')]) }}</p>

    <div class="summary">
        <div>{{ __('Total Revenue') }}: {{ currency_format($totalRevenue) }}</div>
        <div>{{ __('Orders') }}: {{ $ordersCount }}</div>
        <div>{{ __('Average Order Value') }}: {{ currency_format($averageOrder) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Invoice #') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Payment') }}</th>
                <th>{{ __('Subtotal') }}</th>
                <th>{{ __('Discount') }}</th>
                <th>{{ __('Tax') }}</th>
                <th>{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $sale->date?->format('d M Y H:i') }}</td>
                    <td>{{ $sale->customer?->name ?? __('Walk-in Customer') }}</td>
                    <td>{{ ucfirst($sale->payment_type) }}</td>
                    <td>{{ currency_format($sale->items->sum('total')) }}</td>
                    <td>{{ currency_format($sale->discount) }}</td>
                    <td>{{ currency_format($sale->tax) }}</td>
                    <td>{{ currency_format($sale->total_amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

