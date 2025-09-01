<?php

namespace App\Livewire\User;

use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Models\Category as ModelCategory;

class Category extends Component
{
    public function getData()
    {
        return ModelCategory::all();
    }
    public function storeData($data)
    {
        try {
            $validation = validator($data, [
                'name' => 'required|min:3|max:25|unique:categories,name'
            ])->validate();

            ModelCategory::create($validation);
            return 'Category Created Successfully';
        }
        catch(ValidationException $exception){
            return response()->json($exception->errors());
        }


    }
    public function render()
    {
        return view('livewire.user.category');
    }
}
