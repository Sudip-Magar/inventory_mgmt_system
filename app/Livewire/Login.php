<?php

namespace App\Livewire;

use Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    public $email,$password;

    public function check(){
        $validation = $this->Validate([
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

        if(Auth::attempt($validation)){
            session()->flash('success','Login Successfull');
            return $this->redirectRoute('home');
        }
        else{
            session()->flash('error','invalid credential');
            return redirect()->back();
        }

    }
    public function render()
    {
        return view('livewire.login');
    }
}
