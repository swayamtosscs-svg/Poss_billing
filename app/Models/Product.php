<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'stock',
        'price',
        'barcode',
        'sku',
        'image_path',
        'description',
    ];

    protected $appends = [
        'thumbnail_url',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->image_path) {
            if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
                return $this->image_path;
            }

            $relativeAssetsPath = 'assets/' . ltrim($this->image_path, '/');
            $absoluteAssetsPath = public_path($relativeAssetsPath);
            if (file_exists($absoluteAssetsPath)) {
                return asset($relativeAssetsPath);
            }

            if (Storage::disk('assets')->exists($this->image_path)) {
                return asset($relativeAssetsPath);
            }

            if (Storage::disk('public')->exists($this->image_path)) {
                return Storage::disk('public')->url($this->image_path);
            }

            if (file_exists(public_path($this->image_path))) {
                return asset($this->image_path);
            }
        }

        $initial = Str::upper(Str::substr($this->name ?? 'P', 0, 1));
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#ff8a4c;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#ef4444;stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="120" height="120" rx="16" fill="url(#grad)"/>
    <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="54" font-family="Inter, Arial, sans-serif" fill="#ffffff" font-weight="700">{$initial}</text>
</svg>
SVG;

        return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
    }
}
