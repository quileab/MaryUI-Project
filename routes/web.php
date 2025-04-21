<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

// route to web /
Route::get('/', function () {
  return view('index');
});

Volt::route('/login', 'login')->name('login');

Route::get('/logout', function () {
  auth()->logout();
  request()->session()->invalidate();
  request()->session()->regenerateToken();
  request()->session()->flush();
  return redirect('/');
});

// Protected routes
Route::middleware('auth')->group(function () {
  Volt::route('/users', 'users.index');
});
