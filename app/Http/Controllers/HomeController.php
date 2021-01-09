<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyShare;
use App\Models\Shareholder;
use App\Models\StockPrice;
use App\Models\Stock;
use App\Models\StockSector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $notice = [
            'title' => 'Some good news',
            'message' => 'We are coming up with an updated application. The old site has been moved <a href="http://old.nepse.today"target="_blank" rel="noopener noreferrer">here</a>',
        ];
        
        // $sectors = StockSector::with(['stocks','price:close_price,last_updated_price,transaction_date,stock_id'])->get();
        $transactions = DB::table('stock_prices as pr')
            ->join('stocks as s', function($join){
                $date = StockPrice::max('transaction_date');
                $join->on('s.id','pr.stock_id')
                    ->where('pr.transaction_date', $date);
            })
            ->join('stock_sectors as ss','ss.id','s.sector_id')
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

        return view('welcome',
        [
            'sectors' => $sector_summary->sortBy('sector'),
            'turnovers' => $top10Turnovers,
            'trades' => $top10Trades,
            'gainers' => $top10Gainers,
            'loosers' => $top10Loosers,
            'transactions' => $transactions,
            'last_updated_time' => Carbon::parse($last_updated_time),
            'notice' => $notice,
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
}
