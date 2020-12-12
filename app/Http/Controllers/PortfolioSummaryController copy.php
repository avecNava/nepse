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

        $portfolios = DB::table('portfolio_summaries')
            ->join('stocks', 'stocks.id', '=', 'portfolio_summaries.stock_id')
            ->join('shareholders', function($join) use($shareholder_id){
                $join->on('shareholders.id', '=', 'portfolio_summaries.shareholder_id')
                    ->whereIn('shareholders.id', $shareholder_id);
            })
            ->leftJoin('stock_prices', 'stock_prices.stock_id', '=', 'portfolio_summaries.stock_id')
            ->select('portfolio_summaries.*','stocks.*', 'shareholders.*','stock_prices.*')
            ->where('stock_prices.transaction_date','=', $transaction_date)            
            ->orderBy('stocks.symbol')
            ->get();

        $portfolio_by_shareholders = $portfolios->groupBy(function($item){
            return $item->first_name . ' '. $item->last_name;
        });

        dd($portfolio_by_shareholders);

        return view("portfolio.portfolio-summary", 
            [
                'portfolios' => $portfolio_by_shareholders,
                'shareholders' => $shareholders,
                'shareholder_id' => empty($member) ? 0 : $member,
                'transaction_date' => $transaction_date,
            ]
        );
        
    }
    
}
