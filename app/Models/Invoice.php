<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'date',
        'customer_name',
        'customer_address',
        'customer_phone',
        'customer_email',
        'status', // draft or final
        'total_amount',
        'total_discount',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
        'total_discount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getNetTotalAttribute()
    {
        return $this->total_amount - $this->total_discount;
    }
}