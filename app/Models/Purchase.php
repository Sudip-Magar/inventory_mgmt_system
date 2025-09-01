<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnArgument;

class Purchase extends Model
{
    protected $fillable = [
        'vendor_id',
        'total_amount',
        'quantity',
        'order_date',
        'status'
    ];

    public function vendors(){
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseItem(){
        return $this->hasMany(PurchaseItems::class);
    }
}
