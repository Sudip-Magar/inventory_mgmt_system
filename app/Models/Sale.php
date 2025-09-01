<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable =[
        'customer_id',
        'total_amount',
        'quantity',
        'sales_date'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function salesItem(){
        return $this->hasMany(SaleItems::class);
    }
}
