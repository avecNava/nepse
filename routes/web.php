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
use App\Http\Controllers\FeedbackController;
use App\Models\Shareholder;
use App\Models\MyShare;
use Carbon\Carbon;
use App\Models\User;
use Jenssegers\Agent\Agent;

Auth::routes([
    'verify' => true,
    'register' => true,
]);
    
    
// Auth::loginUsingId(4);

Route::get('test', function(Request $request){

    $date = Carbon::now();
    return $date->toDateString();
    
    // $agent = new Agent();

    // $objAgent = [
    //     'ip' => \Request::ip(),
    //     'device'=> $agent->device(),
    //     'desktop'=>$agent->isDesktop(),
    //     'phone'=>$agent->isPhone(),
    //     'robot'=>$agent->isRobot(),
    //     'browser'=> $agent->browser(),
    //     'browser_version'=> $agent->version($agent->browser()),
    //     'platform'=> $agent->platform(),
    //     'platform_version'=> $agent->version($agent->platform()),
    // ];
    // dd($objAgent);
});


Route::get('sample-record', function(){
    $shareholder = Shareholder::find(19);
    event(new \App\Events\CreateSampleRecordsEvent($shareholder->id));
    return "Sample record created for Shareholder<br/>" . $shareholder->toJson(JSON_PRETTY_PRINT) ;
});

Route::get('mail', function(){
    $user = User::find(1);
    event(new \App\Events\UserRegisteredEvent($user));
    // event(new \App\Events\UserRegisteredEvent($user));
    //$user->notify(new \App\Notifications\UserRegistrationNotification($user));
    // Notification::send($user,new \App\Notifications\UserRegistrationNotification($user));
    // Notification::send($user,new \App\Notifications\UserVerifyNotification($user));
    // $feedback = Feedback::find(1);
    // return new App\Mail\FeedbackMail($feedback);
    // return new App\Mail\WelcomeMail($user);
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

Route::get('sales',[SalesController::class,'view']);
Route::post('sales/store',[SalesController::class,'store']);

Route::get('basket',[SalesBasketController::class,'view']);
Route::get('basket/add',[SalesBasketController::class,'create']);
Route::post('basket/store',[SalesBasketController::class,'store']);
Route::post('basket/update',[SalesBasketController::class,'update']);
Route::get('basket/delete/{id}',[SalesBasketController::class,'delete']);
Route::get('basket/{username}/{id?}',[SalesBasketController::class,'view']);

Route::get('guidelines', [HomeController::class, 'guideline']);
Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedback');
Route::post('feedbacks', [FeedbackController::class, 'store']);
Route::get('feedback/view/{id}', [FeedbackController::class, 'feedback']);


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::fallback(function() {
    return 'Ouch 🙄';
});