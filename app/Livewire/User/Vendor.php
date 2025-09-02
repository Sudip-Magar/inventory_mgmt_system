<?php

namespace App\Livewire\User;

use App\Models\Vendor as ModelVendor;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\ValidationException;

class Vendor extends Component
{
    use WithFileUploads;
    public $image;

    public function getData()
    {
        return ModelVendor::latest()->get();
    }

    public function storeData($data)
    {
        try {
            $data['image'] = $this->image;
            $validation = validator($data, [
                'name' => 'required|min:3|max:20',
                'email' => 'required|email',
                'address' => 'required|min:3|max:50',
                'company' => 'required|min:3|max:50',
                'phone' => 'required|numeric|digits:10',
                'image' => 'nullable|image'
            ])->validate();

            if ($validation['image']) {
                $validation['image'] = $validation['image']->store('customer', 'public');
            }

            ModelVendor::create($validation);
            $this->reset();
            return "Vendor Created Successfully";
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }
    }

    public function updateVendordata($data)
    {

        try {
            $vendor = ModelVendor::findOrFail($data['id']);
            $data['image'] = $this->image;
            $validation = validator($data, [
                'name' => 'required|min:3|max:20',
                'email' => 'required|email',
                'address' => 'required|min:3|max:50',
                'phone' => 'required|numeric|digits:10',
                'image' => 'nullable|image'
            ])->validate();

            if ($validation['image']) {
                $validation['image'] = $validation['image']->store('customer', 'public');
            } else {
                unset($validation['image']); // keep existing image
            }
            $vendor->update($validation);
            $this->reset();
            return "Vendor Updated Successfully";
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }

    }

    public function DeleteVendor($id){
        ModelVendor::findOrFail($id)->delete();
        return "Vendor Deleted Successfull";
    }

    public function render()
    {
        return view('livewire.user.vendor');
    }
}
