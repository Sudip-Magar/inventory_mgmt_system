<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'image',
        'email',
        'address',
        'company',
        'phone'
    ];
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }
}
