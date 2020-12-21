<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Services\UtilityService;
use Illuminate\Support\Collection;
use App\Models\MeroShare;
use App\Models\StockOffering;
use App\Models\PortfolioSummary;
use App\Models\Portfolio;
use App\Models\Sales;
use App\Models\Shareholder;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class MeroShareController extends Controller
{
     public function __construct()
     {
         $this->middleware('auth');
     }

   /**
    * displays the share import form and meroshare transaction listing for various shareholders
    * parameter : $shareholder_id 
    */
   public function importTransactionForm($shareholder_id = null)
   {   
          $user_id = Auth::id();
          //if no shareholder-id was supplied, choose parent account as default
          if(empty($shareholder_id)){
               $shareholder_id = Shareholder::where('parent_id', $user_id)->pluck('id')->first();
          }
          
          //get all the shareholder names to display in the select input
          $shareholders = Shareholder::where('parent_id', $user_id)->get();
          
          //get transaction history and its related stock_id, security_name from related (stocks table)
          $transactions = Meroshare::where('shareholder_id', $shareholder_id)
                         ->with(['share'])
                         ->get();
          
          return view('meroshare.import-transaction', 
          [
               'transactions' => $transactions,
               'shareholders' => $shareholders->sortBy('first_name'),
               'shareholder_id' => $shareholder_id,
          ]);
   }
   
   /**
    * Read from uploaded excel file and save data to meroshare_transactions table
    */
   public function importTransactions(Request $request)
   {
          $validator = $request->validate([
               'shareholder' => 'required',
               'file' => 'required'
          ]);

          // $destinationPath = base_path('public/menu');
          $destinationPath = storage_path('app/meroshare');
          $file_name = $request->file('file')->getClientOriginalName();
          // $extension = $request->file('file')->extension();      //retuns txt for html , sql files...
          
          //get the file extension from filename manually
          $tmp = explode('.', $file_name);
          $extension = $tmp[ sizeof($tmp)-1 ];

          // Valid File Extensions
          $valid_extension = ["txt","csv","xls","xlsx"];

          // Check file extension
          if( !in_array(strtolower($extension),$valid_extension, true)){
               return redirect()->back()->with('error','File type not supported. Please provide an XLSX or a CSV file');   
          }
          
          $new_name = UtilityService::serializeTime() .'-'. UtilityService::serializeString($file_name);
          $request->file('file')->move($destinationPath, $new_name);
          $pathToCSV = $destinationPath .'/'. $new_name;
          
          $transactions = collect();
          $shareholder_id = $request->input('shareholder');
          $rows = SimpleExcelReader::create($pathToCSV)->getRows();        // $rows is an instance of Illuminate\Support\LazyCollection

          try {
              
          
               $rows->each(function(array $row) use ($transactions, $shareholder_id) {

                    $remarks = $row['History Description'];
                    
                    $transactions->push( 
                         array(
                              'symbol' => $row['Scrip'], 
                              'transaction_date' => $row['Transaction Date'],
                              'credit_quantity' => Str::of( $row['Credit Quantity'] )->contains('-') ? null : $row['Credit Quantity'],
                              'debit_quantity' => Str::of( $row['Debit Quantity'] )->contains('-') ? null : $row['Debit Quantity'],
                              'transaction_mode' => $this->getTransactionMode($remarks),
                              'offer_type' => $this->getOfferType($remarks),
                              'remarks' => $remarks,
                              'shareholder_id' =>$shareholder_id
                         )
                    );
               });

               //remove existing records with the given shareholder_id
               MeroShare::where('shareholder_id', $shareholder_id)->delete();
               
               //add new records
               MeroShare::importTransactions($transactions);

          } catch (\Throwable $th) {
               $error = [
                    'message' => $th->getMessage(),
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                ];
                Log::error('Import error',$error);
                return redirect()->back()->with('error', "Import error! Did you use the right document? <br>Error message: " . $error['message']);
          }

          return redirect()->back()->with('success', 'Selected records have been imported successfully 👌');   
        
   }


   public static function getOfferType($str){
     
     $offering_txt = Str::lower($str);

     if(Str::contains($offering_txt,'ca-bonus')){
          return 'BONUS';
     }
     elseif(Str::contains($offering_txt,'ca-rights')){
          return 'RIGHTS';
     }
     elseif(Str::contains($offering_txt,'ca-merger')){
          return 'MERGER';
     }
     elseif(Str::containsAll($offering_txt,['initial public offering','fpo'])) {
          return 'FPO';
     }
     elseif(Str::contains($offering_txt,['initial public offering','ipo'])) {
          return 'IPO';
     }
     elseif(Str::contains($offering_txt,'demat')){
          return 'IPO';
     }
     elseif(Str::containsAll($offering_txt,['on-cr td','tx','set'])){
          return 'SECONDARY';
     }
     elseif(Str::containsAll($offering_txt,['on-dr td','tx','set'])){
          return 'SALES';
     }
     elseif(Str::contains($offering_txt,'on-cr td')) {
          return 'SECONDARY';
     }
     else {
          return 'OTHERS';
     }
     
   }


   public static function getTransactionMode($str){
     
     $txt = Str::lower($str);

     if(Str::contains($txt,'on-dr td')){
          return 'Dr';
     }
     elseif(Str::contains($txt,['on-cr td','cr current balance','credit'])) {
          return 'Cr';
     }
     else {
          return '-';
     }
     
   }

     /**
     * This function is called via AJAX POST method 
     * when "Import to Poftfolio" is clicked on http://dev.nepse/meroshare/transaction route
     * Input parameters (Request object with trans_ids and shareholder_id)
     * The trans_ids and related data are stored into the Portfolio table for the given shareholder_id
     * 
     */
     public function storeToPortfolio(Request $request)
     {
          
          if( empty($request->trans_id) ){

               return response()->json([
                    'status' => 'error',
                    'message' => 'Confused 👀 Did you select any record at all?',
                ]);

          }

          $portfolios = (new MeroShareController)->parseTransactions($request);
          
          //1. parse purchase data
          $purchase_arr = [
               'BONUS',
               'RIGHTS',
               'RIGHT',
               'FPO',
               'IPO',
               'SECONDARY',
          ];

          $purchases = $portfolios->filter(function($item, $key) use($purchase_arr){
               return in_array($item['offer_code'], $purchase_arr);
          });

          //2. insert/update Portfolios table
          if($purchases->isNotEmpty()){
               Portfolio::updateOrCreatePortfolios($purchases);
          }
          
          //3. parse sales data
          $sales_arr = [
               'SALES',
               'SALE',
               'MERGER',
               'OTHER',
               'OTHERS',
          ];

          $sales = $portfolios->filter(function($item, $key) use($sales_arr){
               return in_array($item['offer_code'], $sales_arr);
          });
          
          //4. insert/update Portfolios table
          if($sales->isNotEmpty()){
               Sales::updateOrCreateSales($sales);
          }

          //5. parse purchase and sales
          //get shareholder_id and stock_id in each group
          //insert/update portfolio summary table with the aggregated data
          if($portfolios->isNotEmpty()){

               $portfolios->groupBy('stock_id')->filter(function($item, $key){
                    $row = $item->first();                       //we only need the first object in the group
                    $stock_id = $key;                            //key is the stock_id as the collection is grouped by stock_id
                    $shareholder_id = $row['shareholder_id'];
                    PortfolioSummary::updateCascadePortfoliSummaries($shareholder_id, $stock_id);
               });
          }
     
          return response()->json([
               'message' => count($portfolios) . " records have been imported to your portfolio 👌",
               'count' => count($portfolios),
          ]);

     }


     /**
     * loop the request, parse  data and create records in Portfolios table
     */

     public static function parseTransactions(Request $request)
     {
         
          $user_id = Auth::id();         
          $portfolios = collect([]);                               //data for portfolio table

          //trans_id is comma separated (eg, 1,2,3,4,5), explode into array 
          $ids = Str::of($request->trans_id)->explode(',');
          
          // Get portfolio from meroshare_transactions table, related data from Shares and Offers table 
          //consturct collections to store data
          $transactions = 
               MeroShare::whereIn('id', $ids->toArray())
               ->with(['share:id,symbol','offer:id,offer_code'])         //relationships (share, offer)
               ->get();
          
          //group by data by symbol; loop each symbol group; aggregate the debit and credit quantities
          $transactions = $transactions->groupBy('symbol');

          $transactions->map(function($item) use($portfolios){

               $total_dr = 0;
               $total_cr = 0;

               foreach ($item as $value) {
                    
                    //add up the total debit and credit for each symbols within a group
                    $total_cr += empty($value->credit_quantity) ? 0 : $value->credit_quantity;
                    $total_dr += empty($value->debit_quantity) ? 0 : $value->debit_quantity;
                    
                    $row = array(
                         'quantity' => empty($value->credit_quantity) ? $value->debit_quantity : $value->credit_quantity,
                         'shareholder_id' => $value->shareholder_id,
                         'symbol' =>  empty($value->share) ? null : $value->share->symbol,
                         'stock_id' =>  empty($value->share) ? null : $value->share->id,
                         'offer_id' =>  empty($value->offer) ? null : $value->offer->id,                      //get from related table
                         'offer_code' =>  empty($value->offer) ? null : $value->offer->offer_code,            //get from related table
                         'transaction_date' => $value->transaction_date,
                         'remarks' => $value->remarks,
                    );  

                    $portfolios->push( $row );
                    
               };
               
          });

          return $portfolios;

     }

}
