<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurhchaseReturnItem extends Model
{
    protected $fillable = [
        'purhchase_return_id',
        'product_id',
        'quantity',
        'cost_price',
        'subTotal',
        'disount_amt',
        'return_reason',
    ];

    public function purhchaseReturn()
    {
        return $this->belongsTo(PurhchaseReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
