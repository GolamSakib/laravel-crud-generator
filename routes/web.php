<?php

use Illuminate\Support\Facades\Cache;
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

Route::get('/test-cache', function () {
    try {
        Cache::put('test_key', 'test_value', 60);
        $value = Cache::get('test_key');
        return 'Cache working: ' . $value;
    } catch (Exception $e) {
        return 'Cache failed: ' . $e->getMessage();
    }
});




