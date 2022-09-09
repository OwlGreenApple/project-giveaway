<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),
    'bank_name'=>'BCA',
    'bank_owner'=>'Sugiarto Lasjim',
    'currency'=>'IDR',
    'email_admin'=>'info@topleads.app',
    'phone_admin'=>'+62817-318-368',
    'no_rek'=>'8290-336-261',
    'WAMATE_EMAIL'=>env('WAMATE_EMAIL'),
    'WAMATE_URL'=>env('WAMATE_URL')
];
