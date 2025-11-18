<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'type',
        'party_id',
        'paymentable_type',
        'paymentable_id',
        'payment_date',
        'amount',
        'payment_method',
        'bank_account_id',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function paymentable()
    {
        return $this->morphTo();
    }
}
