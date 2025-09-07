<?php

namespace App\Livewire\User;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Purchase as ModelPurchase;
use Livewire\Component;

class Purchase extends Component
{

    public function getdata(){
        $vendor = Vendor::latest()->get();
        $purchase = ModelPurchase::latest()->get();
        $product = Product::latest()->get();
        $discount = Discount::latest()->get();
        return [
            $vendor,
            $purchase,
            $product,
            $discount,
        ];
    }
    public function render()
    {
        return view('livewire.user.purchase');
    }
}
