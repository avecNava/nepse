<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockPriceController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesBasketController;
use App\Http\Controllers\MeroShareController;
use App\Http\Controllers\MyShareController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PortfolioSummaryController;
use App\Models\PortfolioSummary;
use App\Http\Controllers\FeedbackController;
use App\Models\Shareholder;
use App\Models\Portfolio;
use App\Models\MyShare;
use App\Models\StockPrice;
use Carbon\Carbon;
use App\Models\User;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Notifications\Notifiable;

Auth::routes([
    'verify' => true,
    'register' => true,
]);
    
// Auth::loginUsingId(6);
Auth::loginUsingId(171);

Route::get('test', function(){

    
    //$feedback = \App\Models\Feedback::first();
    // dd($feedback->description);
    //Mail::to($user)->send(new \App\Mail\FeedbackMail($feedback));
});


Route::get('sample-record', function(){
    $shareholder = Shareholder::find(53);
    event(new \App\Events\CreateSampleRecordsEvent($shareholder->id));
    return "Sample record created for Shareholder<br/>" . $shareholder->toJson(JSON_PRETTY_PRINT) ;
});

Route::get('mail', function(){
    $user = User::find(1);
    $user->notify(new \App\Notifications\UserVerifyNotification($user));

});

Route::get('/', [PortfolioSummaryController::class, 'index']);

Route::get('shareholder/{id?}',[ShareholderController::class, 'getShareholder']);
Route::get('shareholder/delete/{id}',[ShareholderController::class, 'delete']);
Route::get('shareholders',[ShareholderController::class, 'index']);
Route::post('shareholders',[ShareholderController::class, 'create']);

Route::get('latest-price', [StockPriceController::class, 'index']);

Route::get('import/share/{shareholder_id?}', [MyShareController::class, 'create']);
Route::post('import/share/store', [MyShareController::class, 'store']);
Route::post('import/share/delete', [MyShareController::class, 'delete']);

Route::get('import/meroshare/{shareholder_id?}', [MeroShareController::class, 'create']);
Route::post('import/meroshare/store', [MeroShareController::class, 'store']);
Route::post('import/meroshare/delete', [MeroShareController::class, 'delete']);

Route::post('share/export/portfolio', [MyShareController::class, 'exportPortfolio']);
Route::post('meroshare/export/portfolio', [MeroShareController::class, 'exportPortfolio']);

Route::get('summary/stocks/{id}', [PortfolioController::class, 'getUserStocks']);

Route::get('commission/{amount}', [PortfolioController::class, 'commission']);
Route::get('portfolio/get/{id}', [PortfolioController::class, 'getPortfolioByID']);

//add, edit portfolio
Route::get('portfolio/new', [PortfolioController::class, 'create']);
Route::post('portfolio/create', [PortfolioController::class, 'store']);
Route::get('portfolio/edit/{id}', [PortfolioController::class, 'edit']);
Route::post('portfolio/edit', [PortfolioController::class, 'update']);
Route::get('portfolio/delete/{id}', [PortfolioController::class, 'delete']);

Route::get('portfolio', [PortfolioSummaryController::class, 'index'])->name('home');

/*put these at the bottom or the portfolio routes*/
Route::pattern('username','[a-zA-Z0-9\-]+');                        //doesn't support unicode
Route::get('portfolio/{username}/{symbol}/{member}', [PortfolioController::class, 'showPortfolioDetails']);
Route::get('portfolio/{username}/{id}', [PortfolioController::class, 'shareholderPortfolio']);

Route::get('sales', [SalesController::class,'view']);
Route::post('sales/store',[SalesController::class,'store']);
Route::get('sales/{username}/{id?}',[SalesController::class,'view']);

Route::get('basket',[SalesBasketController::class,'view']);
Route::get('basket/add',[SalesBasketController::class,'create']);
Route::post('basket/store',[SalesBasketController::class,'store']);
Route::post('basket/update',[SalesBasketController::class,'update']);
Route::post('basket/delete',[SalesBasketController::class,'delete']);
Route::get('basket/{username}/{id?}',[SalesBasketController::class,'view']);

Route::post('sales/store',[SalesController::class,'store']);

Route::get('guidelines', [HomeController::class, 'guideline']);
Route::get('faq', [HomeController::class, 'faq']);
Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedback');
Route::post('feedbacks', [FeedbackController::class, 'store']);
Route::get('feedback/view/{id}', [FeedbackController::class, 'feedback']);


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::fallback(function() {
    return 'Ouch 🙄';
});