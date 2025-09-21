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
        'payment_status',
        'payment_method',
        'total_received_amount',
        'total_due_amount',

    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function salesItem(){
        return $this->hasMany(SaleItems::class);
    }
    public function SalesRetuens(){
        return $this->hasMany(SaleReturn::class);
    }
}
