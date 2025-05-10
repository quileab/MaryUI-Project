<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out.']);
})->middleware('auth:sanctum');
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!auth()->attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    $token = auth()->user()->createToken('API Token')->plainTextToken;

    return response()->json(['token' => $token]);
});
//return user data from id without auth as exmaple
Route::get('/user/{id}', function ($id) {
    $user = \App\Models\User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }
    return response()->json($user);
});
