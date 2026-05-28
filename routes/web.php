<?php

use App\Http\Controllers\RouteController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::resource('routes', RouteController::class);
