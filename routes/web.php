<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockPriceController;
use App\Http\Controllers\NepseIndexController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesBasketController;
use App\Http\Controllers\MeroShareController;
use App\Http\Controllers\MyShareController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PortfolioSummaryController;
use App\Http\Controllers\FeedbackController;
use App\Models\NepseIndex;

Auth::routes([
    'verify' => true,
    'register' => true,
]);
    
// Auth::loginUsingId(7);
// Auth::loginUsingId(171);

Route::get('test', function(){

});

Route::get('mail', function(){
    $user = User::find(1);
    $user->notify(new \App\Notifications\UserVerifyNotification($user));

});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('stock-data', [HomeController::class, 'stockData']);

//redirect from old site registration page
Route::get('account/register', function(){
    return redirect('register');
});

Route::get('users/{role?}', [HomeController::class,'users'])->middleware('admin');
Route::post('users', [HomeController::class,'updateUsers'])->middleware('admin');
Route::get('users/log', [HomeController::class,'userLogs'])->middleware('admin');

Route::get('shareholder/{id?}',[ShareholderController::class, 'getShareholder']);
Route::get('shareholder/delete/{id}', [ShareholderController::class, 'delete']);
Route::get('shareholders',[ShareholderController::class, 'index']);
Route::post('shareholders',[ShareholderController::class, 'create']);

Route::get('latest-price', [StockPriceController::class, 'index']);
Route::get('latest-index', [NepseIndexController::class, 'indexHistory']);
Route::get('current-index', [NepseIndexController::class, 'currentIndex']);
Route::get('what', [NepseIndex::class, 'updateCurrentIndex']);

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
Route::get('portfolio/{uuid}', [PortfolioController::class, 'Portfolio']);

Route::get('sales', [SalesController::class,'view']);
Route::get('sales/new', [SalesController::class,'create']);
Route::post('sales/store', [SalesController::class,'store']);
Route::get('sales/get/{id}',[SalesController::class,'getSales']);
Route::get('sales/{uuid?}',[SalesController::class,'view']);
Route::post('sales/edit',[SalesController::class,'update']);
Route::post('sales/export',[SalesController::class,'export']);
Route::post('sales/mark-sold',[SalesController::class,'markSold']);

Route::post('cart/store',[SalesBasketController::class,'store']);
Route::get('cart/{uuid?}',[SalesBasketController::class,'view']);
Route::get('cart/add',[SalesBasketController::class,'create']);
Route::post('cart/update',[SalesBasketController::class,'update']);
Route::post('cart/delete',[SalesBasketController::class,'delete']);


Route::get('guidelines', [HomeController::class, 'guideline']);
Route::get('faq', [HomeController::class, 'faq']);
Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedback');
Route::post('feedbacks', [FeedbackController::class, 'store']);
Route::get('feedback/view/{id}', [FeedbackController::class, 'feedback']);


Auth::routes();
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::fallback(function() {
    echo '<h1>Country roads take me <a href=' . url('/') .'>HOME</h1>';
    echo '</center>';
});