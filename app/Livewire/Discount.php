<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Discount as ModelDiscount;
class Discount extends Component
{
    public $code, $name, $is_item_wise, $sign, $rate;

    public function store()
    {
        $this->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'sign' => 'required|in:+,-',
            'rate' => 'required|numeric|min:0',
        ]);

        ModelDiscount::create([
            'code' => $this->code,
            'name' => $this->name, // ✅ lowercase matches DB + validation
            'sign' => $this->sign,
            'rate' => $this->rate,
            'is_item_wise' => (bool) $this->is_item_wise,
        ]);

        $this->reset(); // clears form
        session()->flash('success', 'Discount created successfully!');
    }

    public function render()
    {
        return view('livewire.discount', [
            'discounts' => ModelDiscount::latest()->get(),
        ]);
    }
}
