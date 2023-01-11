<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

Route::get('/auth/redirect/{driver}', function ($driver) {

    $isCompany = intval(request()->get('isCompany')) ? 1 : 0;
    if(request()->get('isLogin') == false) {
        return Socialite::driver($driver)->with(['state' => 'isCompany=' . $isCompany])->redirect();
    }
    else return Socialite::driver($driver)->redirect();
});
