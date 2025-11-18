<?php

namespace App\Jobs;

use App\Mail\SaleInvoiceMail;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSaleInvoiceJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected int $saleId
    ) {
    }

    public function handle(): void
    {
        $sale = Sale::with(['items.product', 'customer'])->find($this->saleId);

        if (! $sale || ! $sale->customer || ! $sale->customer->email) {
            return;
        }

        $pdf = Pdf::loadView('sales.invoice-pdf', ['sale' => $sale])->output();

        Mail::to($sale->customer->email)
            ->send(new SaleInvoiceMail($sale, $pdf));
    }
}
