<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'sign',
        'rate',
        'is_item_wise',
    ];

    protected $casts = [
        'is_item_wise' => 'boolean',
        'rate' => 'decimal:2',
    ];
}
