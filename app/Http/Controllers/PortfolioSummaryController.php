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
    
    public function __constructor()
    {
        $this->middleware('auth');        
    }

    /**
     * display the portfolio summary
     * if shareholder_id is null, show portfolio for the parent 
     * otherwise show portfolio for the selected user
     */
    public function index($username, $member = null)
    {
        //if shareholder_id is null, get "ALL Portfolio" [current user and all shareholders under the current user]
        //else load the portfolio for the given shareholder_id
        $user_id = Auth::id();
        $shareholder_id = [$member];       //make the varible array
        if(empty($member)){
            $shareholder_id = Shareholder::where('parent_id', $user_id)->pluck('id')->all();        //return Array            
        }
        
        //lookup data
        $shareholders = Shareholder::where('parent_id', $user_id)->get()    ;       
        $transaction_date = StockPrice::getLastDate();

        // $portfolios = PortfolioSummary::whereIn('shareholder_id', $shareholder_id)
        //                 ->with(['shareholder','share','stockPrice'=>function($q) use($transaction_date) {
        //                     $q->where('transaction_date', '>=', $transaction_date);
        //                   }])->get();
        // return response()->json($portfolios);
        
        //todo: leftJoin is not working fine
        //when stockPrice is not present for a symbol, the share is not displayed in the portfolio
        //eg, TVJCL

        // $portfolios = DB::table('portfolio_summaries')
        //     ->join('stocks', 'stocks.id', '=', 'portfolio_summaries.stock_id')
        //     ->join('shareholders', function($join) use($shareholder_id){
        //         $join->on('shareholders.id', '=', 'portfolio_summaries.shareholder_id')
        //             ->whereIn('shareholders.id', $shareholder_id);
        //     })
        //     ->leftJoin('stock_prices', 'stock_prices.stock_id', '=', 'portfolio_summaries.stock_id')
        //     ->select('portfolio_summaries.*','stocks.*', 'shareholders.*','stock_prices.*')
        //     ->where('stock_prices.transaction_date','=', $transaction_date)            
        //     ->orderBy('stocks.symbol')
        //     ->get();

        $portfolios = DB::table('portfolio_summaries as p')
            ->join('shareholders as s', function($join) use($shareholder_id){
                $join->on('s.id', '=', 'p.shareholder_id')
                    ->whereIn('s.id', $shareholder_id);
            })
            ->join('stocks as st', 'st.id', '=', 'p.stock_id')
            ->leftJoin('stock_prices as pr', 'pr.stock_id', '=', 'p.stock_id')
            ->selectRaw(
                'st.symbol, SUM(p.total_quantity) as total_quantity, AVG(p.wacc_rate) as average_rate,
                CONCAT(s.first_name," ", s.last_name) as shareholder, s.id, s.relation,
                AVG(pr.close_price) as ltp, AVG(pr.last_updated_price) as last_price, AVG(pr.previous_day_close_price) as last_ltp'
                )
            ->where('pr.transaction_date','=', $transaction_date)
            ->groupBy('shareholder','s.id','st.symbol','s.relation')
            ->get();

            //group the resultset by shareholder
            $portfolio_grouped = $portfolios->groupBy(function($item){
                // return $item->id . '-' . $item->symbol;
                return $item->id;
            });
            // dd($portfolio_grouped);
            $portfolio_agg = $portfolio_grouped->map(function ($items, $key) {

                $sum_stocks = 0;
                $sum_quantity = 0;
                $sum_investment = 0;
                $sum_current_worth = 0;
                $sum_prev_worth = 0;
                $sum_rate = 0;

                foreach ($items as $item) {
                    $sum_stocks += 1;
                    $quantity = $item->total_quantity;
                    $sum_quantity += $quantity;
                    if($item->average_rate){
                        $sum_investment += $quantity * $item->average_rate;
                        $sum_rate += $item->average_rate;
                    }
                    $sum_prev_worth += $quantity * $item->last_ltp;                    
                    $sum_current_worth = $item->ltp ? $quantity * $item->ltp : $quantity * $item->last_price;
                }

                $wacc_rate = $sum_rate / $sum_stocks;
                $difference = $sum_current_worth - $sum_prev_worth;
                $diff_class = '';
                if($difference > 0){ $diff_class = 'increase';} elseif($difference<0){$diff_class='decrease';}
                $diff_per ='' ;
                if($sum_prev_worth > 0){
                    $diff_per = round(($difference / $sum_prev_worth)*100 ,2);
                }
                $gain = $sum_current_worth - $sum_investment;
                $gain_class = '';
                if($gain > 0){ $gain_class = 'increase';} elseif($gain<0){$gain_class='decrease';}
                $gain_per = '';
                if($sum_investment > 0){
                    $gain_per = round(($gain/$sum_investment)*100 ,2);
                }

                return ([
                    'shareholder' => $item->shareholder,
                    'relation' => $item->relation,
                    'shareholder_id' => $item->id,
                    'stocks' => $sum_stocks,
                    'quantity' => $sum_quantity,
                    'effective_rate' => $wacc_rate,
                    'investment' => $sum_investment,
                    'current_worth' => $sum_current_worth,
                    'prev_worth' => $sum_prev_worth,
                    'diff' => $difference,
                    'diff_per' => $diff_per,
                    'diff_class' => $diff_class,
                    'gain' => $gain,
                    'gain_per' => $gain_per,
                    'gain_class' => $gain_class,
                ]);

            });

        // dd($portfolio_agg);

        return view("portfolio.portfolio-summary", 
            [
                'portfolio_summary' => $portfolio_agg,
                'shareholders' => $shareholders,
                'shareholder_id' => empty($member) ? 0 : $member,
                'transaction_date' => $transaction_date,
            ]
        );
        
    }
    
}
