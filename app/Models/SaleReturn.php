<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $fillable = [
        'sale_id',
        'return_reason',
        'total_amount',
        'total_quantity',
        'total_discount_amt',
        'total_paid_amount',
        'total_due_amount',
        'status',
        'payment_status',
        'payment_method',
    ];

    public function sale(){
        return $this->belongsTo(Purchase::class);
    }

    public function SaleReturnItems(){
        return $this->hasMany(SaleReturnItem::class);
    }
}
