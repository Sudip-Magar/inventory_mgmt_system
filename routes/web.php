<?php

use App\Livewire\Login;
use App\Livewire\Register;
use App\Livewire\User\Category;
use App\Livewire\User\Customer;
use App\Livewire\User\Home;
use App\Livewire\User\Product;
use App\Livewire\User\Vendor;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', Home::class)->name('home');
    Route::get('/category', Category::class)->name('category');
    Route::get('/product', Product::class)->name('product');
    Route::get('/customer', Customer::class)->name('customer');
    Route::get('/vendor', Vendor::class)->name('vendor');
});
