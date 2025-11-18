<?php

namespace App\Exports;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected Carbon $from,
        protected Carbon $to,
        protected array $filters = []
    ) {
    }

    public function collection(): Collection
    {
        $query = Sale::with(['customer', 'items.product'])
            ->whereBetween('date', [$this->from->clone()->startOfDay(), $this->to->clone()->endOfDay()]);

        $query = $this->applyFilters($query);

        return $query->orderBy('date')->get();
    }

    public function map($sale): array
    {
        $items = $sale->items->map(fn ($item) => ($item->product?->name ?? 'Product') . ' × ' . $item->quantity)->join(', ');

        return [
            '#' . str_pad($sale->id, 5, '0', STR_PAD_LEFT),
            $sale->date?->format('d M Y H:i'),
            $sale->customer?->name ?? 'Walk-in Customer',
            ucfirst($sale->payment_type),
            number_format($sale->items->sum('total'), 2),
            number_format($sale->discount, 2),
            number_format($sale->tax, 2),
            number_format($sale->total_amount, 2),
            $items,
        ];
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Date',
            'Customer',
            'Payment Type',
            'Subtotal',
            'Discount',
            'Tax',
            'Total',
            'Items',
        ];
    }

    private function applyFilters(Builder $query): Builder
    {
        return $query
            ->when($this->filters['customer'] ?? null, fn($q, $customer) => $q->where('customer_id', $customer))
            ->when($this->filters['category'] ?? null, function ($q, $category) {
                $q->whereHas('items.product', fn($builder) => $builder->where('category_id', $category));
            })
            ->when($this->filters['product'] ?? null, function ($q, $product) {
                $q->whereHas('items', fn($builder) => $builder->where('product_id', $product));
            });
    }
}
