<?php

namespace App\Listeners;

use App\Events\SaleRecorded;
use App\Jobs\SendSaleInvoiceJob;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStockAndLoyalty implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SaleRecorded $event): void
    {
        $sale = $event->sale->loadMissing('items', 'customer');

        foreach ($sale->items as $item) {
            Product::whereKey($item->product_id)->decrement('stock', $item->quantity);
        }

        if ($sale->customer) {
            $pointsEarned = (int) floor($sale->total_amount / 100);
            if ($pointsEarned > 0) {
                $sale->customer->increment('points', $pointsEarned);
            }

            if ($sale->customer->email) {
                SendSaleInvoiceJob::dispatch($sale->id);
            }
        }
    }
}
