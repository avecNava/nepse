<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyShare;
use App\Models\Shareholder;
use App\Models\StockPrice;
use App\Models\Stock;
use App\Models\AppLog;
use App\Models\User;
use App\Models\DailyIndex;
use App\Models\NepseIndex;
use App\Models\StockSector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\UtilityService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $name = UtilityService::parseFirstName(optional(Auth::user())->name);
        $notice = [
            'title' => "Hey $name",
            'message' => 'We have revamped the website. The old site has been moved <a href="http://old.nepse.today"target="_blank" rel="noopener noreferrer">here</a>',
        ];
        
        // $sectors = StockSector::with(['stocks','price:close_price,last_updated_price,transaction_date,stock_id'])->get();
        $transactions = DB::table('stock_prices as pr')
            ->join('stocks as s', function($join){
                $date = StockPrice::max('transaction_date');
                $join->on('s.id','pr.stock_id')
                    ->where('pr.transaction_date', $date);
            })
            ->leftjoin('stock_sectors as ss','ss.id','s.sector_id')
            ->select('s.symbol', 's.security_name',
                'pr.*',
                'ss.id as sector_id','ss.sector'
            )
            ->orderByDesc('pr.last_updated_time')
            ->get();
        
        //todo: get stock price and sector again using left join
        $sector_summary = $transactions->groupBy('sector_id')->map(function($item){
            $row = $item->first();            
            return collect([
                'sector' => $row->sector,
                'total_qty' => $item->sum('total_traded_qty'),
                'total_value' => $item->sum('total_traded_value'),
            ]);
        });

        $top10Turnovers = $transactions->sortByDesc('total_traded_value')->take(10);
        $top10Trades = $transactions->sortByDesc('total_traded_qty')->take(10);

        // dd($top10Turnovers);
        $stocks = $transactions->map(function($stock){            
            $change_per = 0;
            $change = $stock->last_updated_price - $stock->previous_day_close_price;
            if($stock->previous_day_close_price>0){
                $change_per = round(($change/$stock->previous_day_close_price)*100,2);
            }
            return collect([
                'symbol' => $stock->symbol,
                'security_name' => $stock->security_name,
                'ltp' => $stock->last_updated_price,
                'change' => $change,
                'change_per' => $change_per,
                ]);                   
        });
        
        $top10Gainers= $stocks->sortByDesc('change_per')->take(10);
        $top10Loosers= $stocks->sortBy('change_per')->take(10);
        $last_updated_time = StockPrice::max('last_updated_time');

        $last_trade_date = Str::of($last_updated_time)->substr(0,10);

        return view('welcome',
        [
            'sectors' => $sector_summary->sortByDesc('total_value'),
            'turnovers' => $top10Turnovers,
            'trades' => $top10Trades,
            'gainers' => $top10Gainers,
            'loosers' => $top10Loosers,
            'totalScrips' => $transactions->count('symbol'),
            'last_updated_time' => Carbon::parse($last_updated_time),
            'notice' => $notice,
            'totalTurnover' => StockPrice::where('created_at','>=', $last_trade_date)->sum('total_traded_value'),
            'currentIndex' => NepseIndex::where('transactionDate','>=', $last_trade_date)->orderByDesc('transactionDate')->take(1)->first(),
            'prevIndex' => NepseIndex::where('transactionDate','<', $last_trade_date)->orderByDesc('transactionDate')->take(1)->first(),

        ]);
    }

    /**
     * sends JSON formatted output (GOOGLE CHART Style) to the response
     * Reference : https://developers.google.com/chart/interactive/docs/reference#dataparam
     * Nava Bogatee
     * 08 Feb 2021
     */
    public function getIndexJson()
    {
       
        $cols = array(
            ["id"=>"Date","label"=>"Date","pattern"=>"","type"=>"datetime"],
            ["id"=>"","label"=>"Index","pattern"=>"","type"=>"number"],
        );
    
        $hourlyIndex=DailyIndex::get();
        $last_record = $hourlyIndex->last();
        $epoch = $last_record->epoch;
        $datetime_str = UtilityService::epochToTimeZone($epoch, config('app.timezone', 'Asia/Kathmandu'));
        
        $rows = $hourlyIndex->map(function( $item, $key){
            
            $epoch = $item->epoch;
            $date_str = new \DateTime(date("Y-m-d H:i:s", substr($epoch, 0, 10)));
          
            $year = $date_str->format("Y");
            $month = $date_str->format("m");
            $monthName = $date_str->format("F");
            $day = $date_str->format("d");
            $hours = $date_str->format("H");
            $minutes = $date_str->format("i");
            $seconds = $date_str->format("s");

            return
            [
                'c' => [
                    ['v' => "Date($year, $month, $day, $hours, $minutes, $seconds)", 'f' => "$monthName $day $hours:$minutes"],
                    ['v' => $item->index, 'f' => null], 
                ]
            ];
        });
        
        return response()->json(
            ['indexHistory'=>
                ['cols' => $cols,
                 'rows' => $rows 
                ],
             'epoch' => $last_record->epoch,
             'dateString' => $datetime_str,
             'index' => $last_record->index,
             ]);
    }

    public function stockData()
    {

        $name = UtilityService::parseFirstName(optional(Auth::user())->name);

        // $sectors = StockSector::with(['stocks','price:close_price,last_updated_price,transaction_date,stock_id'])->get();
        $transactions = DB::table('stock_prices as pr')
            ->join('stocks as s', function($join){
                $date = StockPrice::max('transaction_date');
                $join->on('s.id','pr.stock_id')
                    ->where('pr.last_updated_time','>=', $date);
            })
            ->leftjoin('stock_sectors as ss','ss.id','s.sector_id')
            ->select('s.symbol', 's.security_name',
                'pr.*',
                'ss.id as sector_id','ss.sector'
            )
            ->orderByDesc('pr.last_updated_time')
            ->get();
        
        // $transactions->dd();

        $sector_summary = $transactions->groupBy('sector_id')->map(function($item){
            
            $row = $item->first();            
            return collect([
                'sector' => $row->sector,
                'total_qty' => $item->sum('total_traded_qty'),
                'total_value' => $item->sum('total_traded_value'),
            ]);
        });

        $top10Turnovers = $transactions->sortByDesc('total_traded_value')->take(10);
        $top10Trades = $transactions->sortByDesc('total_traded_qty')->take(10);

        // dd($top10Turnovers);

        $stocks = $transactions->map(function($stock){            
            $change_per = 0;
            $change = $stock->last_updated_price - $stock->previous_day_close_price;
            if($stock->previous_day_close_price>0){
                $change_per = round(($change/$stock->previous_day_close_price)*100,2);
            }
            return collect([
                'symbol' => $stock->symbol,
                'security_name' => $stock->security_name,
                'ltp' => $stock->last_updated_price,
                'change' => $change,
                'change_per' => $change_per,
                ]);                   
        });
        
        $top10Gainers= $stocks->sortByDesc('change_per')->take(10);
        $top10Loosers= $stocks->sortBy('change_per')->take(10);
        $last_updated_time = StockPrice::max('last_updated_time');

        return view('stock-data',
        [
            'sectors' => $sector_summary->sortBy('sector'),
            'turnovers' => $top10Turnovers,
            'trades' => $top10Trades,
            'gainers' => $top10Gainers,
            'loosers' => $top10Loosers,
            'transactions' => $transactions,
            'last_updated_time' => Carbon::parse($last_updated_time),
        ]);
    }

    public function guideline()
    {
        return view('guidelines');
    }

    public function faq()
    {   
        return view('faq');
    }

    public function users($role = "all")
    {   
        $users = null;
        if(Str::contains($role, 'all')) {
            $users = User::all()->sortByDesc('created_at');
        }else {
            $users = User::where('user_role', $role)->get();
        }   
        return view('users.list-user', ['users' => $users]);
    }
    
    public function updateUsers(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        if(!empty($user)){
            $user->user_role = $request->role;
            $user->active = $request->active ? true : false;
            $user->save();
            AppLog::createLog([
                'module' => 'HomeController',
                'title' =>'UPDATE User',
                'desc' => json_encode(['id'=>$request->id, 'name'=>$request->name,'role' => $user->user_role, 'active' => $user->active]),
            ]);
            return redirect()->back()->with('message','User updated successfully');
        }
        return redirect()->back()->with('error','User not updated');
    }

    public function userLogs()
    {
        $users = null;
        if(Str::contains($role, 'all')) {
            $users = User::all()->sortByDesc('created_at');
        }else {
            $users = User::where('user_role',$role)->get();
        }   
        return view('users.user-log', ['users' => $users]);
    }
}
