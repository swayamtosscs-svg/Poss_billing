<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'phone',
        'email',
        'gstin',
        'billing_address',
        'shipping_address',
        'opening_balance',
        'balance_type',
        'credit_limit',
        'credit_days',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getCurrentBalance()
    {
        $purchases = $this->purchases()->sum('total_amount');
        $paid = $this->payments()->where('type', 'out')->sum('amount');
        return $this->opening_balance + $purchases - $paid;
    }
}
