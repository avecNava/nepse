<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockPriceController;
use App\Http\Controllers\MeroShareController;
use App\Http\Controllers\PortfolioController;


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

Route::get('latest-price', [StockPriceController::class, 'index']);
Route::get('meroshare/transaction', [MeroShareController::class, 'importTransactionForm']);
Route::post('meroshare/transaction', [MeroShareController::class, 'importTransaction']);
Route::post('meroshare/import-transaction', [PortfolioController::class, 'storeToPortfolio']);
Route::get('meroshare/import-transaction', [PortfolioController::class, 'portfolio']);
