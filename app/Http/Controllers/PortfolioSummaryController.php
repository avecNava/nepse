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

        $portfolios = DB::table('portfolios as p')
            ->join('shareholders as s', function($join) use($shareholder_id){
                $join->on('s.id', '=', 'p.shareholder_id')
                    ->whereIn('s.id', $shareholder_id);
            })
            ->join('stocks as st', 'st.id', '=', 'p.stock_id')
            ->leftJoin('stock_prices as pr', 'pr.stock_id', '=', 'p.stock_id')
            ->selectRaw(
                'st.symbol, SUM(p.quantity) as total_quantity, AVG(p.effective_rate) as average_rate, SUM(p.total_amount) as total_amount,
                CONCAT(s.first_name," ", s.last_name) as shareholder, s.id, s.relation,
                AVG(pr.close_price) as ltp, AVG(pr.last_updated_price) as last_price, AVG(pr.previous_day_close_price) as last_ltp'
                )
            ->where('pr.transaction_date','=', $transaction_date)
            ->groupBy('shareholder','s.id','st.symbol','s.relation')
            ->get();

            //group the resultset by shareholder_id and symbol
            $portfolio_grouped = $portfolios->groupBy(function($item){
                // return $item->id . '-' . $item->symbol;
                return $item->id;
            });

            $portfolio_agg = $portfolio_grouped->map(function ($items, $key) {

                $sum_stocks = 0;
                $sum_quantity = 0;
                $sum_total_amount = 0;
                $sum_current_worth = 0;
                $sum_prev_worth = 0;

                foreach ($items as $item) {
                    $sum_stocks += 1;
                    $quantity = $item->total_quantity;
                    $sum_total_amount = $item->total_amount;
                    $sum_quantity += $quantity;
                    $sum_prev_worth += $quantity * $item->last_ltp;
                    $sum_current_worth = $item->ltp ? $quantity * $item->ltp : $quantity * $last_price;
                }

                return ([
                    'shareholder' => $item->shareholder,
                    'relation' => $item->relation,
                    'id' => $item->id,
                    'stocks' => $sum_stocks,
                    'quantity' => $sum_quantity,
                    'total_amount' => $sum_total_amount,
                    'current_worth' => $sum_current_worth,
                    'prev_worth' => $sum_prev_worth,
                    'change' => round($sum_current_worth / $sum_prev_worth, 2),
                    'gain' => round($sum_current_worth - $sum_prev_worth, 2),
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
