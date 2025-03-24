<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\BookController@main');
Route::get('/books/{any?}', 'App\Http\Controllers\BookController@main')->where('any', '.*');
Route::post('/books/{any?}', 'App\Http\Controllers\BookController@main')->where('any', '.*');

Route::get('/users/{any?}', 'App\Http\Controllers\UsersController@main')->where('any', '.*');
Route::post('/users/{any?}', 'App\Http\Controllers\UsersController@main')->where('any', '.*');