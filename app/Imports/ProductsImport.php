<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array<string, mixed> $row
     */
    public function model(array $row)
    {
        $name = Arr::get($row, 'name');
        if (! $name) {
            return null;
        }

        $categoryName = trim((string) Arr::get($row, 'category', 'General'));
        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            ['description' => 'Imported category']
        );

        $product = Product::firstOrNew([
            'barcode' => Arr::get($row, 'barcode'),
        ]);

        $product->fill([
            'name' => $name,
            'category_id' => $category->id,
            'stock' => (int) Arr::get($row, 'stock', 0),
            'price' => (float) Arr::get($row, 'price', 0),
            'sku' => Arr::get($row, 'sku') ?: Str::upper(Str::random(8)),
            'description' => Arr::get($row, 'description'),
        ]);

        $product->save();

        return $product;
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string'],
            '*.price' => ['nullable', 'numeric'],
            '*.stock' => ['nullable', 'integer'],
        ];
    }
}
