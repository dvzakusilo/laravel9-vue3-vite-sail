<?php

use Domains\Varnish\Controllers\VarnishController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('varnish.')
    ->prefix('varnish')
    ->group(function (){
        Route::get('/', [VarnishController::class, 'index'])->name('index');
        Route::get('/scan/', [VarnishController::class, 'scan'])->name('scan');
        Route::get('/scan/sitemap/', [VarnishController::class, 'scanSitemap'])->name('sitemap');
    });


