<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/create', [App\Http\Controllers\HomeController::class, 'create_giveaway']);
Route::get('/account', [App\Http\Controllers\HomeController::class, 'accounts']);
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact']);
Route::get('/scan', [App\Http\Controllers\HomeController::class, 'connect_wa']);
