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

//AUTH
Route::get('register-redirect',[App\Http\Controllers\Auth\RegisterController::class, 'register_redirect']);
Route::post('pass_reset', [App\Http\Controllers\Auth\RegisterController::class, 'reset'])->name('pass-reset');

//ORDER
Route::get('thankyou', [App\Http\Controllers\OrderController::class, 'thankyou']);
Route::get('checkout/{id?}', [App\Http\Controllers\OrderController::class, 'index']);
Route::post('submit_payment',[App\Http\Controllers\OrderController::class, 'submit_payment'])->middleware('check_order');
Route::get('summary',[App\Http\Controllers\OrderController::class, 'summary']);
Route::post('loginajax',[App\Http\Controllers\Auth\LoginController::class, 'loginAjax']);// user 

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboard']);
Route::get('/list-contestants/{event_id}', [App\Http\Controllers\HomeController::class, 'contestants']);
Route::get('/contestant/{event_id}', [App\Http\Controllers\HomeController::class, 'get_contestant']);
Route::get('/del-contestant', [App\Http\Controllers\HomeController::class, 'del_contestant']);
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact']);
Route::post('/contact-admin', [App\Http\Controllers\HomeController::class, 'save_contact']);

// Route::get('/test', [App\Http\Controllers\ApiController::class, 'mailchimp_valid_api']);

/* ACOUNTS */
Route::get('/scan', [App\Http\Controllers\HomeController::class, 'connect_wa']);
Route::get('/account/{id?}', [App\Http\Controllers\HomeController::class, 'accounts']);
Route::get('/orders', [App\Http\Controllers\HomeController::class, 'order_list']);
Route::post('order-confirm-payment',[App\Http\Controllers\OrderController::class, 'confirm_payment_order']);
Route::post('/update-profile', [App\Http\Controllers\HomeController::class, 'update_profile'])->middleware('check_user_profile');
Route::post('/save-api', [App\Http\Controllers\HomeController::class, 'save_api']);

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
Route::post('/delete-broadcast', [App\Http\Controllers\BroadcastController::class, 'delete_broadcast']);
Route::get('/edit-broadcast/{id}', [App\Http\Controllers\BroadcastController::class, 'edit_broadcast']);


/* Admin */
Route::get('list-user',[App\Http\Controllers\AdminController::class, 'index']);
Route::get('list-order',[App\Http\Controllers\AdminController::class, 'order_list']);
Route::get('order-load',[App\Http\Controllers\AdminController::class,'order']);
Route::get('order-confirm',[App\Http\Controllers\AdminController::class,'confirm_order']);
Route::get('ban-user',[App\Http\Controllers\AdminController::class,'ban_user']);
Route::get('load-user',[App\Http\Controllers\AdminController::class,'display_users']);

/* Affiliate */
Route::get('/create-affiliate', [App\Http\Controllers\AffiliateController::class, 'create_affiliate']);
Route::post('/save-affiliate', [App\Http\Controllers\BroadcastController::class, 'save_affiliate']);