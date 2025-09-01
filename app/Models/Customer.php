<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'image',
        'email',
        'address'
    ];

    public function sales(){
        return $this->hasMany(Sale::class);
    }
}
