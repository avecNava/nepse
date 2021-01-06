<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Shareholder;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use App\Models\StockPrice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
        $scripts ='';
        $members = '';

        $notice = [
            'title' => 'Attention',
            'message' => 'Please verify your stocks as there may be some errors during import from the old system.',
        ];

        $transaction_date = StockPrice::getLastDate();

        $portfolios = DB::table('portfolio_summaries as p')
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
                        's.id as shareholder_id, s.relation, s.uuid,
                        CONCAT(s.first_name," ", s.last_name) as shareholder, 
                        st.symbol, 
                        p.*,
                        pr.close_price, pr.last_updated_price, pr.previous_day_close_price'
                    )                    
                    ->orderBy('s.first_name','asc')
                    ->get();
            
            //aggregates
            $total_investment = $portfolios->sum('investment');
            // dd($total_investment);
            $net_worth = $portfolios->sum(function($item){
                $close_price = $item->last_updated_price ?  $item->last_updated_price  : $item->close_price;
                return $item->quantity * $close_price;
            });
            $net_gain = $net_worth - $total_investment;
            $total_shareholders = $portfolios->unique('shareholder_id');
            $total_scripts = $portfolios->unique('stock_id');

            $score_card = collect([
                'total_investment' => $total_investment,
                'net_worth' => $net_worth,
                'net_gain' => $net_gain,
                'net_gain_per' => $total_investment ? ($net_gain/$total_investment)*100 :'',
                'net_gain_css' => $net_gain > 0 ? 'positive' : 'negative',
                'shareholders' => $total_shareholders->count(),
                'total_scripts' =>  $total_scripts->count(),
            ]);
            
            //group the resultset by shareholder
            $shareholders = $portfolios->groupBy(function($item){
                return $item->shareholder_id .'-' . $item->shareholder;
            });
            
            
            //loop each Shareholder data and calculate aggregates
            $portfolio_agg = $shareholders->map(function ($items, $key) {
               
                $row = $items->first();

                $total_scripts = $items->count();

                $total_units = $items->sum(function($row){
                    return $row->quantity;
                });
                
                $total_investment = $items->sum(function($item){
                    return $item->quantity * $item->wacc;
                });
                // echo $key;
                // dd($total_investment);
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
                
                return  
                
                    collect([

                        'uuid' => $row->uuid,
                        'shareholder' => $row->shareholder,
                        'relation' => $row->relation,
                        'total_scripts' => $total_scripts,
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
                'transaction_date' => $transaction_date,
                'scorecard' => $score_card,
                'notice' => $notice,
            ]);
        
    }

}
