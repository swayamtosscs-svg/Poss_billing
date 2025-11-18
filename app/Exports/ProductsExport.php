<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected array $filters = []
    ) {
    }

    public function collection(): Collection
    {
        $query = Product::with('category')->orderBy('name');

        if ($search = $this->filters['search'] ?? null) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $this->filters['category_id'] ?? null) {
            $query->where('category_id', $categoryId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Category',
            'Stock',
            'Price',
            'Barcode',
            'SKU',
            'Description',
            'Created At',
        ];
    }

    /**
     * @param \App\Models\Product $product
     */
    public function map($product): array
    {
        return [
            $product->name,
            optional($product->category)->name,
            $product->stock,
            $product->price,
            $product->barcode,
            $product->sku,
            $product->description,
            $product->created_at?->format('Y-m-d H:i'),
        ];
    }
}
