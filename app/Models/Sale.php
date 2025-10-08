<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable =[
        'customer_id',
        'total_amount',
        'quantity',
        'sales_date',
        'expected_date',
        'status',
        'notes',
        'payment_status',
        'payment_method',
        'total_received_amount',
        'total_due_amount',
        'total_discount_amt',

    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function salesItems(){
        return $this->hasMany(SaleItems::class);
    }
    public function SalesReturns(){
        return $this->hasMany(SaleReturn::class);
    }
}
