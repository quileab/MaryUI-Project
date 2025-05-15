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
  Volt::route('/register', 'register')->name('register');
  Volt::route('/users', 'users.index');
  Volt::route('/carlos', 'carlos');
  Volt::route('/posts', 'posts.index');
  Volt::route('/posts/crud', 'posts.crud');
  Volt::route('/posts/{post}/edit', 'posts.crud');

  Route::get('/clear/{option?}', function ($option = null) {
    $logs = [];
    // if option is 'prod' then run composer install --optimize-autoloader --no-dev
    if ($option == 'prod') {
      $logs['Composer Install for PROD'] = Artisan::call('composer install --optimize-autoloader --no-dev');
    }

    $maintenance = ($option == "cache") ? [
      'Flush' => 'cache:flush',
    ] : [
      //'DebugBar'=>'debugbar:clear',
      'Storage Link' => 'storage:link',
      'Config' => 'config:clear',
      'Optimize Clear' => 'optimize:clear',
      'Optimize' => 'optimize',
      'Route Clear' => 'route:clear',
      'Route Cache' => 'route:cache',
      'View Clear' => 'view:clear',
      'View Cache' => 'view:cache',
      'Cache Clear' => 'cache:clear',
      'Cache Config' => 'config:cache',
    ];

    foreach ($maintenance as $key => $value) {
      try {
        Artisan::call($value);
        $logs[$key] = '✔️';
      } catch (\Exception $e) {
        $logs[$key] = '❌' . $e->getMessage();
      }
    }
    return "<pre>" . print_r($logs, true) . "</pre><hr />";
  });
});
