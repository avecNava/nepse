<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockPriceController;
use App\Http\Controllers\MeroShareController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PortfolioSummaryController;
use App\Mail\WelcomeMail;
// use App\Models\Portfolio;
// use App\Models\StockPrice;
// use App\Models\PortfolioSummary;
// use  Carbon\Carbon;

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
// Auth::routes();
Auth::routes(['register' => true]);        //disable user registration

Route::get('/welcome', function(){
    // return view('welcome');
    $user = Auth::user();
    return Mail::to($user)->send(new WelcomeMail($user));
});

// Route::get('mail', function(){
//     $user = Auth::user();
//     return new App\Mail\WelcomeMail($user);
// });

Route::get('/', [PortfolioSummaryController::class, 'index']);
Route::get('guidelines', [HomeController::class, 'guideline']);



Route::get('shareholder/{id?}',[ShareholderController::class, 'getShareholder']);
Route::get('shareholder/delete/{id}',[ShareholderController::class, 'delete']);
Route::get('shareholders',[ShareholderController::class, 'index']);
Route::post('shareholders',[ShareholderController::class, 'create']);

Route::get('latest-price', [StockPriceController::class, 'index']);
Route::get('meroshare/transaction/{shareholder_id?}', [MeroShareController::class, 'importTransactionForm']);
Route::post('meroshare/transaction', [MeroShareController::class, 'importTransactions']);
Route::post('meroshare/store-portfolio', [MeroShareController::class, 'storeToPortfolio']);


Route::pattern('username','[a-zA-Z0-9\-]+');      //doesn't support unicode
Route::get('portfolio/{username}/{symbol}/{member}', [PortfolioController::class, 'showPortfolioDetails']);
Route::get('{username}/dashboard/{id}', [PortfolioController::class, 'shareholderDashboard']);
Route::get('portfolio/user/{id}', [PortfolioController::class, 'getUserStocks']);
// Route::get('portfolio/{view}', [PortfolioController::class, 'showPortfolioDetails']);

Route::get('portfolio/commission/{amount}', [PortfolioController::class, 'commission']);
Route::get('portfolio/get/{id}', [PortfolioController::class, 'getPortfolioByID']);
Route::get('portfolio/delete/{id}', [PortfolioController::class, 'delete']);

//add, edit portfolio
Route::get('portfolio/new', [PortfolioController::class, 'create']);
Route::post('portfolio/create', [PortfolioController::class, 'store']);
Route::get('portfolio/edit/{id}', [PortfolioController::class, 'edit']);
Route::post('portfolio/edit', [PortfolioController::class, 'update']);

Route::get('portfolio', [PortfolioSummaryController::class, 'index'])->name('home');
// Route::get('portfolio/{username}/{member}', [PortfolioSummaryController::class, 'index']);



Route::get('test',function(){
   
    $symbols = ['ADBL','AHPC','AIL','AKJCL','AKPL','ALBSL','ALICL','API'];
    $date_str = '2020-12-17';
    $result = StockPrice::whereIn('symbol', $symbols)
                ->where('transaction_date','<>',$date_str)
                ->where('latest',true)
                ->update(['latest' => false]);

    return $result;

    // $symbol = 'cndbl';
    // return \App\Models\StockPrice::where('symbol', $symbol)
    //     ->LastTradePrice()
    //     ->with(['share'])
    //     ->first();
    // return PortfolioSummary::where('quantity','<=',0)->delete();
    // return Portfolio::where('shareholder_id', 16)
    //                 ->where('stock_id', 113)->get();
    // return Portfolio::where($shareholder_id, $stock_id)->sum('quantity')
    // $offers =['IPO','RIGHTS'];
    // return in_array('RIGHTS', $offers) ? 'EXISTS' :'DOES NOT EXIST';
    // return Stock::select('id')->where('symbol', 'API')->first();
    // $hasPortfolio = Portfolio::where('shareholder_id',1)->select('shareholder_id')->withCount(['shareholder_id'])->get();
    // dd($hasPortfolio);

});


Route::get('contact-us',function(){
    return '<p>Hi</p>
            <p>Please write to : nava.bogatee@gmail.com <br/>until we come up with the contact us page 🙏
            <br/><br/>Thank you
            </p>';
})->name('contact-us');

