<?php

use App\Models\Setting;

if (! function_exists('currency_code')) {
    function currency_code(): string
    {
        static $code = null;
        if ($code !== null) {
            return $code;
        }

        $code = Setting::where('key', 'store.currency')->value('value') ?? config('app.currency', 'INR');

        return $code;
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(?string $code = null): string
    {
        $code = strtoupper($code ?: currency_code());

        return match ($code) {
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $code . ' ',
        };
    }
}

if (! function_exists('currency_format')) {
    function currency_format(float|int $amount, int $decimals = 2): string
    {
        return currency_symbol() . number_format($amount, $decimals);
    }
}

if (! function_exists('format_points')) {
    function format_points(int $points): string
    {
        return number_format($points) . ' pts';
    }
}

