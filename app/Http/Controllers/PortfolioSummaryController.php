<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Shareholder;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use App\Models\NepseIndex;
use App\Models\StockPrice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\UtilityService;


class PortfolioSummaryController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']); 
    }

    /**
     * display the portfolio summary
     */
    public function index()
    {
        $user_id = Auth::id();
      
        $net_prev_gain = '';
        $diff = '';
        $sectors = '';
        $scrips ='';
        $members = '';

        $portfolios = 
            DB::table('portfolio_summaries as p')
            ->join('shareholders as s', function($join){
                $join->on('s.id', '=', 'p.shareholder_id')
                    ->where('s.tenant_id', session()->get('tenant_id'));
            })
            ->join('stocks as st', 'st.id', '=', 'p.stock_id')
            ->leftJoin('stock_prices as pr', function($join){
                $join->on('pr.stock_id', 'p.stock_id')
                    ->where('pr.latest', 1);
            })
            ->selectRaw(
                's.id as shareholder_id, s.relation, s.uuid, s.gender,
                CONCAT(s.first_name," ", s.last_name) as shareholder, 
                st.symbol, st.security_name,
                p.*,
                pr.close_price, pr.last_updated_price, pr.previous_day_close_price'
            )                  
            ->orderBy('s.first_name','asc')
            ->get();
        
        //filter out stocks with qty = 0
        $filtered = $portfolios->filter(function($value, $key){
            return $value->quantity > 0;
        });

        //aggregates
        $total_investment = $filtered->sum('investment');
        
        $net_worth = $filtered->sum(function($item){
            $close_price = $item->last_updated_price ?  $item->last_updated_price  : $item->close_price;
            return $item->quantity * $close_price;
        });

        $net_gain = $net_worth - $total_investment;
        $total_shareholders = $filtered->unique('shareholder_id');
        $total_scrips = $filtered->unique('stock_id');

        $score_card = collect([
            'total_investment' => $total_investment,
            'net_worth' => $net_worth,
            'net_gain' => $net_gain,
            'net_gain_per' => $total_investment ? ($net_gain/$total_investment)*100 :'',
            'net_gain_css' => $net_gain > 0 ? 'increase' : 'decrease',
            'shareholders' => $total_shareholders->count(),
            'total_scrips' =>  $total_scrips->count(),
        ]);
        
        //group the resultset by shareholder
        $portfolio_grouped = $filtered->groupBy(function($item){
            return $item->shareholder_id;
        });
        
        //unique stocks
        $unique_stocks = $filtered->unique('symbol');
        
        $last_trade_date = NepseIndex::max('transactionDate');
        $prev_trade_date = NepseIndex::where('transactionDate','<', $last_trade_date)->orderByDesc('transactionDate')->select('transactionDate')->first()->toArray()['transactionDate'];
        
        return view("portfolio.portfolio-summary", 
        [
            'portfolio_summary' => $this->shareholderSummary($portfolio_grouped),
            'scorecard' => $score_card,
            'top_grossing' => $this->topGrossing($unique_stocks),
            'top_gains' => $this->topGainers($unique_stocks),
            'notice' => UtilityService::getNotice(),
            'index' => NepseIndex::getCurrentIndex(),
            'prevIndex' => NepseIndex::where('transactionDate','<', $last_trade_date)->orderByDesc('transactionDate')->select('closingIndex')->first(),
            'totalTurnover' => StockPrice::where('transaction_date','=', $last_trade_date)->sum('total_traded_value'),
            'prevTurnover' => StockPrice::where('transaction_date','=', $prev_trade_date)->sum('total_traded_value'),
        ]);
        
    }

    //enumerate given shareholder and do the aggregations
    private function shareholderSummary($portfolios){
        
        return

        $portfolios->map(function ($items, $key) {

            //get an instance of first row
            $row = $items->first();   
            
            //total stocks
            $total_scrips = $items->count();

            //total quantity of stocks
            $total_units = $items->sum(function($row){
                return $row->quantity;
            });
            
            //total investment
            $total_investment = $items->sum(function($item){
                return $item->quantity * $item->wacc;
            });
            
            //current worth
            $current_worth = $items->sum(function($item){
                $ltp = $item->last_updated_price ?  $item->last_updated_price : $item->close_price;
                return $item->quantity * $ltp;
            });
            
            //previous worth
            $prev_worth = $items->sum(function($item){
                return $item->quantity * $item->previous_day_close_price;
            });

            $day_gain = $current_worth - $prev_worth;
            $day_gain_pc = $prev_worth ? ($day_gain/$prev_worth)*100 : 0;
            $gain = $current_worth - $total_investment;
           
            $gain_pc = $total_investment ? ($gain/$total_investment)*100 : 0;
            $day_gain_class = ''; $gain_class = ''; $change_class ='';

            if($day_gain > 0) {$day_gain_class='increase';} elseif($day_gain < 0) {$day_gain_class='decrease';}
            if($day_gain > 0) {$change_class='increase';} elseif($day_gain < 0) {$change_class='decrease';}
            if($gain_pc > 0) {$gain_class='increase';} elseif($gain_pc < 0) {$gain_class='decrease';}

            
            return  collect([
                'uuid' => $row->uuid,
                'shareholder' => $row->shareholder,
                'relation' => $row->relation,
                'gender' => $row->gender ? $row->gender : 'M' ,
                'total_scrips' => $total_scrips,
                'total_units' => $total_units,
                'total_investment' => $total_investment,
                'current_worth' => $current_worth,
                'prev_worth' => $prev_worth,
                'day_gain' => $day_gain,
                'day_gain_pc' => round($day_gain_pc,2),
                'day_gain_css' => $day_gain_class,
                'change_css' => $change_class,
                'gain' => $gain,
                'gain_pc' => round($gain_pc,2),
                'gain_css' => $gain_class,
            ]);

        })->sortByDesc('total_investment');

    }

    //top grossing stocks
    private function topGrossing($stocks, $rows=10){
        return 
        $stocks->map(function($items, $key){
            $ltp = $items->last_updated_price ?  $items->last_updated_price : $items->close_price;
               $temp = collect([
                   'name' =>$items->security_name,
                   'symbol' =>$items->symbol,
                   'worth' => $ltp * $items->quantity ,
                   'ltp' => $ltp,
               ]);
           return $temp->sortByDesc('worth');
       })->take($rows);
    }
    
    //top gaining stocks
    private function topGainers($stocks, $rows=10){
        return 
        $stocks->map(function($items, $key){
            $ltp = $items->last_updated_price ?  $items->last_updated_price : $items->close_price;
            $prev_price = $items->previous_day_close_price;
            $change_per = 0; $change_class='';
            if($prev_price > 0){
                $change_per = ($ltp - $prev_price)/$prev_price;
                $change_class = $change_per > 0 ? 'increase' : 'decrease';
            }
            return collect([
                'name' =>$items->security_name,
                'symbol' =>$items->symbol,
                'worth' => $ltp * $items->quantity ,
                'quantity' => $items->quantity ,
                'ltp' => $ltp,
                'prevLtp' => $items->previous_day_close_price,
                'change_per' => $change_per*100,
                'change_css' => $change_class,
            ]);
         
       })->sortByDesc('change_per')->take($rows);
    }

}
