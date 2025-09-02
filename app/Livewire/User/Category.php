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

    public function delete($id)
    {
        try {
            ModelCategory::findOrFail($id)->delete();
            return "Category Deleted Successfull";

        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }

    }

    public function updateStore($data)
    {
        try {

            $validation = validator($data, [
                'name' => 'required|min:3|max:25'
            ])->validate();
            $save = ModelCategory::findOrFail($data['id']);
            $save->update($validation);
            $this->reset();
            return "Category Updated Scuccessfully";
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }
    }
    public function storeData($data)
    {
        
        try {
            $validation = validator($data, [
                'name' => 'required|min:3|max:25|unique:categories,name',
                'description'=>'nullable|max:100'
            ])->validate();

            ModelCategory::create($validation);
            $this->reset();
            return 'Category Created Successfully';
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }


    }
    public function render()
    {
        return view('livewire.user.category');
    }
}
