<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\StockMovement as ModelStockMovement;

class StockMovement extends Component
{
    public function render()
    {
        return view('livewire.user.stock-movement',[
            'stockMovement' => ModelStockMovement::with('product')->latest()->get()
        ]);
    }
}
