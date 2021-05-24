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
            
        //aggregates
        $total_investment = $portfolios->sum('investment');
        
        $net_worth = $portfolios->sum(function($item){
            $close_price = $item->last_updated_price ?  $item->last_updated_price  : $item->close_price;
            return $item->quantity * $close_price;
        });

        $net_gain = $net_worth - $total_investment;
        $total_shareholders = $portfolios->unique('shareholder_id');
        $total_scrips = $portfolios->unique('stock_id');

        $score_card = collect([
            'total_investment' => $total_investment,
            'net_worth' => $net_worth,
            'net_gain' => $net_gain,
            'net_gain_per' => $total_investment ? ($net_gain/$total_investment)*100 :'',
            'net_gain_css' => $net_gain > 0 ? 'positive' : 'negative',
            'shareholders' => $total_shareholders->count(),
            'total_scrips' =>  $total_scrips->count(),
        ]);
        
        //group the resultset by shareholder
        $shareholders = $portfolios->groupBy(function($item){
            return $item->shareholder_id;
        });
        
         //1. top 5 grossing share for current shareholder ($top_grossing will be indexed by shareholder_id)
         $top_grossing = $shareholders->map(function($item){
           $temp =
            $item->map(function($row){
                $ltp = $row->last_updated_price ?  $row->last_updated_price : $row->close_price;
                return collect([
                    'name' =>$row->security_name,
                    'id' =>$row->id,
                    'symbol' =>$row->symbol,
                    'worth' => $ltp * $row->quantity ,
                    'quantity' => $row->quantity ,
                    'ltp' => $ltp,
                ]);
            });
            return $temp->sortByDesc('worth')->take(5);
        });

        //2. top gains
        $gain_loss = $shareholders->map(function($item){
            $temp =
            $item->map(function($row){
                $ltp = $row->last_updated_price ?  $row->last_updated_price : $row->close_price;
                $worth = $ltp * $row->quantity;
                $cost = $row->quantity * $row->wacc;
                return collect([
                    'name' =>$row->security_name,
                    'symbol' =>$row->symbol,
                    'gain' => $worth - $cost ,
                ]);
            });
            return [
                'gain' => $temp->sortByDesc('gain')->take(5),
                'loss' => $temp->sortBy('gain')->take(5),
            ];
        });

        //3.loop each Shareholder data and calculate aggregates
        $portfolio_agg = $shareholders->map(function ($items, $key) {

            $row = $items->first();
            $total_scrips = $items->count();
            $total_units = $items->sum(function($row){
                return $row->quantity;
            });            
            $total_investment = $items->sum(function($item){
                return $item->quantity * $item->wacc;
            });            
            $current_worth = $items->sum(function($item){
                $ltp = $item->last_updated_price ?  $item->last_updated_price : $item->close_price;
                return $item->quantity * $ltp;
            });            
            $prev_worth = $items->sum(function($item){
                return $item->quantity * $item->previous_day_close_price;
            });
            $gain = $current_worth - $total_investment;
            $change = $current_worth - $prev_worth;
            $gain_pc = $total_investment ? ($gain/$total_investment)*100 : 0;
            $change_pc = $prev_worth ? ($change/$prev_worth)*100 : 0;
            $gain_class = ''; $change_class ='';
            if($gain_pc > 0) {$gain_class='increase';} elseif($gain_pc < 0) {$gain_class='decrease';}
            if($change_pc > 0) {$change_class='increase';} elseif($change_pc < 0) {$change_class='decrease';}

            
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
                'gain' => $gain,
                'gain_pc' => round($gain_pc,2),
                'gain_css' => $gain_class,
                'change' => $change,
                'change_pc' => round($change_pc,2),
                'change_css' => $change_class,
            ]);
        });

        return view("portfolio.portfolio-summary", 
        [
            'portfolio_summary' => $portfolio_agg->sortByDesc('total_investment'),
            'scorecard' => $score_card,
            'arr_grossing' => $top_grossing,
            'arr_gainloss' => $gain_loss,
            'notice' => UtilityService::getNotice(),
            'index' => NepseIndex::getCurrentIndex(),
        ]);
        
    }

}
