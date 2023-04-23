<?php

use App\Http\Controllers\subscribers\Table;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

$controller_path = 'App\Http\Controllers';

Route::get('/',$controller_path . '\subscribers\Table@index');

Route::get('/validation',$controller_path . '\ValidationController@showform');
Route::post('/validation',$controller_path . '\ValidationController@validateform');

Route::resource('/subscriber-management', Table::class);
