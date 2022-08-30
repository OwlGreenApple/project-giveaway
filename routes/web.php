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

//AUTH
Route::get('/',[App\Http\Controllers\Auth\RegisterController::class, 'price_page']);
Route::get('/privacy',[App\Http\Controllers\OrderController::class, 'privacy']);
Route::get('/test-email',[App\Http\Controllers\Auth\RegisterController::class, 'test_email']);
Route::get('register-redirect',[App\Http\Controllers\Auth\RegisterController::class, 'register_redirect']);
Route::get('logs-8877', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class,'index']);
Route::post('pass_reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('pass-reset');

// App::setLocale('en');
Route::get('/c/{event_link}/{referal?}', [App\Http\Controllers\ContestController::class, 'contest']);
Route::get('/contest', [App\Http\Controllers\ContestController::class, 'task']);
Route::post('taskdata', [App\Http\Controllers\ContestController::class, 'taskdata']);
Route::post('save-entry', [App\Http\Controllers\ContestController::class, 'save_entry']);
Route::post('save-contestant', [App\Http\Controllers\ContestController::class, 'save_contestant'])->middleware('check_contestants');
Route::get('/confirmation/{cid}', [App\Http\Controllers\ContestController::class, 'confirmation']);
Route::get('/test-contestant', [App\Http\Controllers\ContestController::class, 'test_contestant']);

//ORDER
Route::get('thankyou', [App\Http\Controllers\OrderController::class, 'thankyou']);
Route::get('checkout/{id?}', [App\Http\Controllers\OrderController::class, 'index']);
Route::post('submit_payment',[App\Http\Controllers\OrderController::class, 'submit_payment'])->middleware('check_order');
Route::get('summary',[App\Http\Controllers\OrderController::class, 'summary']);
Route::post('loginajax',[App\Http\Controllers\Auth\LoginController::class, 'loginAjax']);// user

Auth::routes();

// COMMON
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboard']);
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact']);
Route::get('/packages', [App\Http\Controllers\HomeController::class, 'upgrade_package']);
Route::get('/message-list/{ev_id}', [App\Http\Controllers\HomeController::class, 'message_list']);
Route::get('/redeem-money', [App\Http\Controllers\HomeController::class, 'redeem_money']);
Route::post('/redeem-withdraw', [App\Http\Controllers\HomeController::class, 'claim_money'])->middleware('check_redeem');
Route::post('/contact-admin', [App\Http\Controllers\HomeController::class, 'save_contact']);

// Route::get('/test', [App\Http\Controllers\ApiController::class, 'mailchimp_valid_api']);

/* CONTESTANTS */
Route::get('/list-contestants/{event_id}', [App\Http\Controllers\HomeController::class, 'contestants']);
Route::get('/contestant/{event_id}', [App\Http\Controllers\HomeController::class, 'get_contestant']);
Route::get('/export-contestant/{event_id}', [App\Http\Controllers\HomeController::class, 'export_contestants']);
Route::get('/del-contestant', [App\Http\Controllers\HomeController::class, 'del_contestant']);
Route::get('/draw-contestant', [App\Http\Controllers\HomeController::class, 'draw_contestant']);
Route::get('/contestant-winner/{event_id}', [App\Http\Controllers\HomeController::class, 'winner']);

/* ACOUNTS */
Route::get('/account/{id?}', [App\Http\Controllers\HomeController::class, 'accounts']);
Route::get('/orders', [App\Http\Controllers\HomeController::class, 'order_list']);
Route::get('/del-phone', [App\Http\Controllers\HomeController::class, 'del_phone']);
Route::post('order-confirm-payment',[App\Http\Controllers\OrderController::class, 'confirm_payment_order']);
Route::post('/update-profile', [App\Http\Controllers\HomeController::class, 'update_profile'])->middleware('check_user_profile');
Route::post('/save-api', [App\Http\Controllers\HomeController::class, 'save_api'])->middleware('check-phone');
Route::post('/upload-branding', [App\Http\Controllers\HomeController::class, 'save_branding']);

/* DEVICES */
Route::get('/scan', [App\Http\Controllers\DeviceController::class, 'connect_wa']);
Route::get('/qrconnect/{id?}', [App\Http\Controllers\DeviceController::class, 'connect_wa']);
Route::post('/create', [App\Http\Controllers\DeviceController::class, 'create_device']);
Route::get('/connect', [App\Http\Controllers\DeviceController::class, 'connect']);
Route::get('/pair', [App\Http\Controllers\DeviceController::class, 'qrcode']);
Route::get('/device', [App\Http\Controllers\DeviceController::class, 'get_phone_status']);
Route::post('/message', [App\Http\Controllers\DeviceController::class, 'send_message']);
Route::get('/del-device', [App\Http\Controllers\DeviceController::class, 'delete_device']);

/* WABLAS */
Route::get('/info', [App\Http\Controllers\WABlasController::class, 'info']);
Route::get('/message', [App\Http\Controllers\WABlasController::class, 'send_message']);

/* EVENTS */
Route::get('/create', [App\Http\Controllers\HomeController::class, 'create_giveaway']);
Route::get('/edit-event/{id}', [App\Http\Controllers\HomeController::class, 'edit_event']);
Route::get('/duplicate-events', [App\Http\Controllers\HomeController::class, 'duplicate_events']);
Route::get('/delete-events', [App\Http\Controllers\HomeController::class, 'del_event']);
Route::post('/save-events', [App\Http\Controllers\HomeController::class, 'save_events'])->middleware('check_events');
Route::get('/promo/{link}', [App\Http\Controllers\HomeController::class, 'promo']);
Route::post('/save-promo', [App\Http\Controllers\HomeController::class, 'save_promo']);

/* Broadcast */
// Route::get('broadcast', [App\Http\Controllers\BroadcastController::class, 'list_broadcast_index']);
Route::get('/create-broadcast', [App\Http\Controllers\BroadcastController::class, 'create_broadcast']);
Route::get('/display-contestants', [App\Http\Controllers\BroadcastController::class, 'display_contestants']);
Route::post('/save-broadcast', [App\Http\Controllers\BroadcastController::class, 'save_broadcast'])->middleware('check_bc');
Route::post('/delete-broadcast', [App\Http\Controllers\BroadcastController::class, 'delete_broadcast']);
Route::get('/edit-broadcast/{id}', [App\Http\Controllers\BroadcastController::class, 'edit_broadcast']);
Route::get('/message-broadcast/{id}', [App\Http\Controllers\BroadcastController::class, 'message_broadcast']);
Route::post('/delete-message', [App\Http\Controllers\BroadcastController::class, 'delete_message']);

/* Admin */
Route::get('list-user',[App\Http\Controllers\AdminController::class, 'index']);
Route::get('list-order',[App\Http\Controllers\AdminController::class, 'order_list']);
Route::get('order-load',[App\Http\Controllers\AdminController::class,'order']);
Route::get('order-confirm',[App\Http\Controllers\AdminController::class,'confirm_order']);
Route::get('ban-user',[App\Http\Controllers\AdminController::class,'ban_user']);
Route::get('load-user',[App\Http\Controllers\AdminController::class,'display_users']);
Route::get('cancel-order',[App\Http\Controllers\AdminController::class,'cancel_order']);
Route::get('settings',[App\Http\Controllers\AdminController::class,'settings']);
Route::get('admin-phone',[App\Http\Controllers\AdminController::class,'display_admin_phone']);
Route::get('admin-phone-del',[App\Http\Controllers\AdminController::class,'del_admin_phone']);
Route::post('save-settings',[App\Http\Controllers\AdminController::class,'settings_save']);
Route::post('save-admin-phone',[App\Http\Controllers\AdminController::class,'settings_phone']);

/* Admin Redeem */
Route::get('affiliate-admin',[App\Http\Controllers\AdminRedeemController::class, 'index']);
Route::get('affiliate-admin-data',[App\Http\Controllers\AdminRedeemController::class,'affiliate_admin_data']);
Route::post('upload-payment-2-redeem',[App\Http\Controllers\AdminRedeemController::class,'upload_payment_2']);


/* Affiliate */
Route::get('/affiliate', [App\Http\Controllers\AffiliateController::class, 'create_affiliate']);
Route::get('/list-affiliate-index', [App\Http\Controllers\AffiliateController::class, 'list_affiliate_index']);
Route::get('/list-affiliate-data', [App\Http\Controllers\AffiliateController::class, 'list_affiliate_data']);
Route::post('/save-affiliate', [App\Http\Controllers\AffiliateController::class, 'save_affiliate']);


Route::get('/{referral_code}',[App\Http\Controllers\Auth\RegisterController::class, 'price_page']);
