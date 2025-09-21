<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnArgument;

class Purchase extends Model
{
    protected $fillable = [
        'vendor_id',
        'total_amount',
        'total_quantity',
        'order_date',
        'expected_date',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'total_discount_amt',
        'total_paid_amount',
        'total_due_amount',
    ];

    // public function vendors(){
    //     return $this->belongsTo(Vendor::class);
    // }

    // public function purchaseItem(){
    //     return $this->hasMany(PurchaseItems::class);
    // }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItems::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurhchaseReturn::class);
    }
}
