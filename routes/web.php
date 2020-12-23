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
use App\Mail\WelcomeMail;
use App\Mail\FeedbackMail;
use App\Models\Feedback;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

// use App\Models\StockPrice;
// use App\Models\PortfolioSummary;
// use  Carbon\Carbon;

// Auth::loginUsingId(1);  
Auth::routes([
    'verify' => true,
    'register' => true,
]);

// Route::post('/email/verification-notification', function (Request $request) {
//     $request->user()->sendEmailVerificationNotification();
//     return back()->with('message', 'Verification link sent!');
// })->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// Route::get('/email/verify', function () {
//     return view('auth.verify');
// })->middleware('auth')->name('verification.notice');

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     return redirect('/login');
// })->middleware(['auth', 'signed'])->name('verification.verify');

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

Route::get('guidelines', [HomeController::class, 'guideline']);
Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedback');
Route::post('feedbacks', [FeedbackController::class, 'store']);
Route::get('feedback/view/{id}', [FeedbackController::class, 'feedback']);

Route::get('test',function(){
    $user = Auth::user();
    return $user->notify(new \App\Notifications\UserRegistration($user));
 
    $symbols = ['ADBL','AHPC','AIL','AKJCL','AKPL','ALBSL','ALICL','API'];
    $date_str = '2020-12-17';
    $result = StockPrice::whereIn('symbol', $symbols)
                ->where('transaction_date','<>',$date_str)
                ->where('latest',true)
                ->update(['latest' => false]);

    return $result;
});




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::fallback(function() {
    return 'Ouch 🙄';
});