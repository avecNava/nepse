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
use App\Models\Stock;
use Carbon\Carbon;
use App\Models\User;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use App\Models\StockSector;
use Illuminate\Support\Facades\DB;

// use Illuminate\Notifications\Notifiable;

Auth::routes([
    'verify' => true,
    'register' => true,
]);
    
// Auth::loginUsingId(7);
// Auth::loginUsingId(171);

Route::get('test', function(){
    return Shareholder::where('uuid','6ea1d6f6-d847-4938-aca9-af40a40f838c')->pluck('parent_id')->first();
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('account/register', function(){
    return redirect('register');
});
Route::get('shareholder/{id?}',[ShareholderController::class, 'getShareholder']);
Route::get('shareholder/delete/{id}', [ShareholderController::class, 'delete']);
Route::get('shareholders',[ShareholderController::class, 'index']);
Route::post('shareholders',[ShareholderController::class, 'create']);

Route::get('latest-price', [StockPriceController::class, 'index']);

Route::get('import/share/{uuid?}', [MyShareController::class, 'create']);
Route::post('import/share/store', [MyShareController::class, 'store']);
Route::post('import/share/delete', [MyShareController::class, 'delete']);

Route::get('import/meroshare/{uuid?}', [MeroShareController::class, 'create']);
Route::post('import/meroshare/store', [MeroShareController::class, 'store']);
Route::post('import/meroshare/delete', [MeroShareController::class, 'delete']);

Route::post('share/export/portfolio', [MyShareController::class, 'exportPortfolio']);
Route::post('meroshare/export/portfolio', [MeroShareController::class, 'exportPortfolio']);

Route::get('summary/{id}', [PortfolioController::class, 'getUserStocks']);

Route::get('commission/{amount}', [PortfolioController::class, 'commission']);
Route::get('portfolio/get/{id}', [PortfolioController::class, 'getPortfolioByID']);

//add, edit portfolio
Route::get('portfolio/new', [PortfolioController::class, 'create']);
Route::post('portfolio/create', [PortfolioController::class, 'store']);
Route::get('portfolio/edit/{id}', [PortfolioController::class, 'edit']);
Route::post('portfolio/edit', [PortfolioController::class, 'update']);
Route::post('portfolio/delete', [PortfolioController::class, 'delete']);

Route::get('dashboard', [PortfolioSummaryController::class, 'index']);
Route::post('portfolio/export', [PortfolioController::class, 'export']);

/*put these at the bottom or the portfolio routes*/
Route::pattern('username','[a-zA-Z0-9\-]+');                        //doesn't support unicode
Route::get('portfolio/{symbol}/{member}', [PortfolioController::class, 'showPortfolioDetails']);
Route::get('portfolio/{uuid}', [PortfolioController::class, 'shareholderPortfolio']);

Route::get('sales', [SalesController::class,'view']);
Route::post('sales/store',[SalesController::class,'store']);
Route::get('sales/{uuid?}',[SalesController::class,'view']);

Route::get('cart/{uuid?}',[SalesBasketController::class,'view']);
Route::get('cart/add',[SalesBasketController::class,'create']);
Route::post('cart/store',[SalesBasketController::class,'store']);
Route::post('cart/update',[SalesBasketController::class,'update']);
Route::post('cart/delete',[SalesBasketController::class,'delete']);

Route::post('sales/store',[SalesController::class,'store']);

Route::get('guidelines', [HomeController::class, 'guideline']);
Route::get('faq', [HomeController::class, 'faq']);
Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedback');
Route::post('feedbacks', [FeedbackController::class, 'store']);
Route::get('feedback/view/{id}', [FeedbackController::class, 'feedback']);


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::fallback(function() {
    echo '<center><h2>Ouch! You seemeed to be lost<h2>';
    echo '<h3>Country roads take me <a href=' . url('/') .'>HOME</h3>';
    echo '</center>';
});