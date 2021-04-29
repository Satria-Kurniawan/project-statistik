<?php

use App\Http\Controllers\StatistikController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('index');
// });

// Route::get('/about', function () {
//     return view('about');
// });

Route::get('/', 'App\Http\Controllers\PagesController@home');
Route::get('/about', 'App\Http\Controllers\PagesController@about');

Route::get('/statistik', 'App\Http\Controllers\StatistikController@index')->name('mahasiswa');
Route::post('/statistik', 'App\Http\Controllers\StatistikController@store');

Route::get('/statistik/edit/{id_mahasiswa}', 'App\Http\Controllers\StatistikController@edit');
Route::put('/statistik/edit/{id_mahasiswa}', 'App\Http\Controllers\StatistikController@update');

Route::get('/statistik/delete/{id_mahasiswa}', 'App\Http\Controllers\StatistikController@delete');
