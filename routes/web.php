<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockPriceController;
use App\Http\Controllers\MeroShareController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\PortfolioController;
use App\Models\Stock;

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
Auth::loginUsingId(1); 

Auth::routes();
Auth::routes(['register' => false]);        //disable user registration
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/welcome', function(){
    return view('welcome');
});

Route::get('shareholder/{id?}',[ShareholderController::class, 'getShareholder']);
Route::get('shareholders',[ShareholderController::class, 'index']);
Route::post('shareholders',[ShareholderController::class, 'create']);

Route::get('latest-price', [StockPriceController::class, 'index']);
Route::get('meroshare/transaction', [MeroShareController::class, 'importTransactionForm']);
Route::post('meroshare/transaction', [MeroShareController::class, 'importTransaction']);
Route::get('meroshare/import-transaction', [PortfolioController::class, 'portfolio']);
Route::post('meroshare/import-transaction', [PortfolioController::class, 'storeToPortfolio']);
Route::get('portfolio/{shareholder_id?}', [PortfolioController::class, 'index']);
Route::get('portfolio/details/{symbol}', [PortfolioController::class, 'portfolioDetails']);










Route::get('last-date', [StockPriceController::class, 'getLastDate']);

Route::get('test',function(){

    return Stock::select('id')->where('symbol', 'API')->first();

});



Route::get('contact-us',function(){
    return '<p>Hi</p>
            <p>Please write to : nava.bogatee@gmail.com <br/>until we come up with the contact us page 🙏
            <br/><br/>Thank you
            </p>';
});

