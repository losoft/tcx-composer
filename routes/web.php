<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', '\App\Http\Controllers\MainController@welcome');

Route::get('news', '\App\Http\Controllers\NewsController@news');

Route::get('workouts', '\App\Http\Controllers\WorkoutController@workouts');
Route::get('workouts/upload', '\App\Http\Controllers\WorkoutController@upload');
Route::post('workouts/upload', '\App\Http\Controllers\WorkoutController@uploaded');

Route::get('merge', '\App\Http\Controllers\MergeController@merge');
