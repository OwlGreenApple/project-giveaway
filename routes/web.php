<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\App;

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

// App::setLocale('en');
Route::get('/c/{event_link}/{referal?}', [App\Http\Controllers\ContestController::class, 'contest']);
Route::get('/contest', [App\Http\Controllers\ContestController::class, 'task']);
Route::post('taskdata', [App\Http\Controllers\ContestController::class, 'taskdata']);
Route::post('save-entry', [App\Http\Controllers\ContestController::class, 'save_entry']);
Route::post('save-contestant', [App\Http\Controllers\ContestController::class, 'save_contestant'])->middleware('check_contestants');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboard']);
Route::get('/contestant/{event_id}', [App\Http\Controllers\HomeController::class, 'get_contestant']);
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact']);
Route::get('/scan', [App\Http\Controllers\HomeController::class, 'connect_wa']);

/* ACOUNTS */
Route::get('/account', [App\Http\Controllers\HomeController::class, 'accounts']);
Route::post('/update-profile', [App\Http\Controllers\HomeController::class, 'update_profile'])->middleware('check_user_profile');

/* EVENTS */
Route::get('/create', [App\Http\Controllers\HomeController::class, 'create_giveaway']);
Route::get('/edit-event/{id}', [App\Http\Controllers\HomeController::class, 'edit_event']);
Route::get('/duplicate-events', [App\Http\Controllers\HomeController::class, 'duplicate_events']);
Route::get('/delete-events', [App\Http\Controllers\HomeController::class, 'del_event']);
Route::post('/save-events', [App\Http\Controllers\HomeController::class, 'save_events'])->middleware('check_events');

/* Broadcast */
Route::get('/create-broadcast', [App\Http\Controllers\BroadcastController::class, 'create_broadcast']);
Route::get('/list-broadcast', [App\Http\Controllers\BroadcastController::class, 'list_broadcast_index']);
Route::post('/save-broadcast', [App\Http\Controllers\BroadcastController::class, 'save_broadcast']);
