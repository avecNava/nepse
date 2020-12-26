<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StockPriceController;
use App\Http\Controllers\MeroShareController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PortfolioSummaryController;
use App\Http\Controllers\FeedbackController;
use App\Models\Shareholder;

Auth::routes([
    'verify' => true,
    'register' => true,
    ]);
    
    
Auth::loginUsingId(1);
Route::get('test',function(){
    return new \App\Mail\WelcomeMail(Auth::user());
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

Route::get('share/import', [HomeController::class, 'importForm']);
Route::post('share/import', [MyShareController::class, 'importShares']);
Route::post('share/store-portfolio', [MyShareController::class, 'storeToPortfolio']);
Route::post('share/delete', [MyShareController::class, 'delete']);

Route::get('meroshare/{shareholder_id?}', [MeroShareController::class, 'importForm']);
Route::post('meroshare/import', [MeroShareController::class, 'importShares']);
Route::post('meroshare/store-portfolio', [MeroShareController::class, 'storeToPortfolio']);
Route::post('meroshare/delete', [MeroShareController::class, 'delete']);


Route::pattern('username','[a-zA-Z0-9\-]+');                        //doesn't support unicode
Route::get('portfolio/{username}/{symbol}/{member}', [PortfolioController::class, 'showPortfolioDetails']);
Route::get('portfolio/{username}/{id}', [PortfolioController::class, 'shareholderPortfolio']);
Route::get('summary/stocks/{id}', [PortfolioController::class, 'getUserStocks']);

Route::get('commission/{amount}', [PortfolioController::class, 'commission']);
Route::get('portfolio/get/{id}', [PortfolioController::class, 'getPortfolioByID']);
Route::get('portfolio/delete/{id}', [PortfolioController::class, 'delete']);

//add, edit portfolio
Route::get('portfolio/new', [PortfolioController::class, 'create']);
Route::post('portfolio/create', [PortfolioController::class, 'store']);
Route::get('portfolio/edit/{id}', [PortfolioController::class, 'edit']);
Route::post('portfolio/edit', [PortfolioController::class, 'update']);

Route::get('portfolio', [PortfolioSummaryController::class, 'index'])->name('home');

Route::get('guidelines', [HomeController::class, 'guideline']);
Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedback');
Route::post('feedbacks', [FeedbackController::class, 'store']);
Route::get('feedback/view/{id}', [FeedbackController::class, 'feedback']);





Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::fallback(function() {
    return 'Ouch 🙄';
});