<?php

namespace App\Livewire\User;

use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Models\Customer as ModelCustomer;
use Livewire\WithFileUploads;

class Customer extends Component
{
    use WithFileUploads;
    public $image;

    public function storeData($data)
    {
        try {
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
            }

            ModelCustomer::create($validation);
            return "Customer Created Successfully";
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }
    }

    public function getData()
    {
        return ModelCustomer::latest()->get();
    }

    public function updateCustomerdata($data)
    {

        try {
            $customer = ModelCustomer::findOrFail($data['id']);
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
            $customer->update($validation);
            return "Customer Updated Successfully";
        } catch (ValidationException $exception) {
            return response()->json($exception->errors());
        }

    }

    public function DeleteProduct($id){
        ModelCustomer::findOrFail($id)->delete();
        return "Customer Deleted Successfull";
    }
    public function render()
    {
        return view('livewire.user.customer');
    }
}
