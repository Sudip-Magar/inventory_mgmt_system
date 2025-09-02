<?php

namespace App\Livewire\User;

use App\Models\Category;
use Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product as ModelProduct;

class Product extends Component
{
    use WithFileUploads;
    public $image;

    public function getdata()
    {
        $category = Category::latest()->get();
        $product = ModelProduct::with('Category')->latest()->get();

        return [
            $category,
            $product
        ];
    }

    public function storeData($datas)
    {
        try {
            $datas['image'] = $this->image;

            $validation = validator($datas, [
                'code' => 'required|min:2|max:10',
                'name' => 'required|min:3|max:20',
                'image' => 'nullable|image',
                'price' => 'required|numeric',
                'description' => 'required|min:3|max:50',
                'cost' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|numeric',
            ])->validate();

            if ($validation['image']) {
                $validation['image'] = $validation['image']->store('product', 'public');
            } else {
                $validation['image'] = null;
            }

            $validation['user_id'] = Auth()->id();
            ModelProduct::create($validation);
            $this->reset();
            return "Product Created Successfully";
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }
    }

    public function updateProduct($data)
    {
        try {
            $product = ModelProduct::findOrFail($data['id']);
            // dd($product);
            $data['image'] = $this->image;

            $validation = validator($data, [
                'code' => 'required|min:2|max:10',
                'name' => 'required|min:3|max:20',
                'image' => 'nullable|image',
                'price' => 'required|numeric',
                'description' => 'required|min:3|max:50',
                'cost' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|numeric',
            ])->validate();

            if ($validation['image']) {
                $validation['image'] = $validation['image']->store('product', 'public');
            }

            $product->update($validation);
            $this->reset();
            return "product Updated Successfull";
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }
    }

    public function deleteProduct($id)
    {
        ModelProduct::findOrFail($id)->delete();
        return "product deleted Successfully";
    }
    public function render()
    {
        return view('livewire.user.product');
    }
}
