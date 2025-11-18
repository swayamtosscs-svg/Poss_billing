<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'store.name' => 'BillingPOS',
            'store.address' => '123 Market Street, City',
            'store.tax_id' => 'TAX-123456',
            'store.currency' => 'INR',
            'store.logo_path' => null,
            'tax.rate' => '18',
            'payment.methods' => json_encode(['cash', 'card', 'upi']),
            'locale.default' => 'en',
        ];
        foreach ($defaults as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
