<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    use WithFileUploads;
    public $name, $image, $phone, $email, $role, $password;

    public function store(){
        $validation = $this->validate([
            'name'=>'required|min:3|max:25',
            'image'=>'nullable|image',
            'phone'=>'required|numeric|digits:10',
            'email'=>'required|email',
            'role'=>'required',
            'password'=>'required|min:6'
        ]);

        if($validation['image']){
            $validation['image'] = $validation['image']->store('users','public');
        }
        else{
            $validation['image'] = null;
        }

        $validation['password'] = Hash::make($validation['password']);
        User::create($validation);
        session()->flash('success','User is Registered Successfully');
        return $this->redirectRoute('login');
    }
    public function render()
    {
        return view('livewire.register');
    }
}
