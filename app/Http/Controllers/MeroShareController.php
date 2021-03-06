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
        $this->middleware(['auth', 'verified']); 
    }

   /**
    * displays the share import form and meroshare transaction listing for various shareholders
    * parameter : $shareholder_id 
    */
   public function create($uuid = null)
   {   
          
          $user_id = Auth::id();
          $shareholder_id = '';
          if(empty($uuid)){
               $shareholder_id = session()->get('shareholder_id');
          }
          else{
               $shareholder_id = Shareholder::where('uuid', $uuid)->pluck('id')->first();
          }
          
          //get all the shareholder names to display in the select input
          $shareholders = Shareholder::where('parent_id', $user_id)->orderBy('first_name')->get();
          
          //get transaction history and its related stock_id, security_name from related (stocks table)
          $transactions = Meroshare::where('shareholder_id', $shareholder_id)
               ->with(['share'])
               ->get();
          
          
          return view('import.meroshare', 
               [
                    'transactions' => $transactions,
                    'shareholders' => $shareholders,
               ]);
   }
   
   /**
    * Read from uploaded excel file and save data to meroshare_transactions table
    */
   public function store(Request $request)
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
          
          try {
          
               $rows = SimpleExcelReader::create($pathToCSV)->getRows();        // $rows is an instance of Illuminate\Support\LazyCollection

               $rows->each(function(array $row) use ($transactions, $shareholder_id) {

                    $remarks = $row['History Description'];
                    $offer_type = $this->getOfferType($remarks);
                    
                    //IGNORE merger (they're debited and credited whhich makes it 0)
                    //But sometimes, other stocks are also marked as MERGER, dont' know why
                    // if ($offer_type != 'MERGER') {
                         if($row['Balance After Transaction'] > 0){
                              $transactions->push( 
                                   array(
                                        'symbol' => $row['Scrip'], 
                                        'transaction_date' => $this->formatDate($row['Transaction Date']),
                                        'credit_quantity' => Str::of( $row['Credit Quantity'] )->contains('-') ? null : $row['Credit Quantity'],
                                        'debit_quantity' => Str::of( $row['Debit Quantity'] )->contains('-') ? null : $row['Debit Quantity'],
                                        'transaction_mode' => $this->getTransactionMode($remarks),
                                        'offer_type' => $offer_type,
                                        'remarks' => $remarks,
                                        'shareholder_id' => $shareholder_id,
                                   )
                              );
                         }
                    // }
               });

               //remove existing records with the given shareholder_id
               MeroShare::where('shareholder_id', $shareholder_id)->delete();

               //add new records
               $success = MeroShare::importTransactions($transactions);
               
               //get uuid for the shareholder
               $user = Shareholder::find( $request->input('shareholder') );

               if(!$success){
                    return redirect("import/meroshare/" . $user->uuid)->with('error', "Import completed. <br>Unfortunately, some of the records could not be imported 👀");
               }
               
               return redirect("import/meroshare/" . $user->uuid)->with('success', 'Records imported successfully 👌 <br/>From the records below,  Select records below and click "Save to Portfolio'); 

          } catch (\Throwable $th) {
               $error = [
                    'message' => $th->getMessage(),
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                    ];
                    Log::error('Import error',$error);
                    return redirect()->back()->with('error', "An error occured 👀 <br> Ensure you uploaded the correct document and column headers are not altered<br/>" . $error['message']);
          }

          try {
               \File::delete( $pathToCSV );
               // unlink( $pathToCSV );               //delete the file
          } catch(\Throwable $th){

          } 
        
   }

   public function formatDate($dateStr){
     if(Str::length($dateStr)==10){
          return $dateStr;
     }

     //if the $dateStr ccomes as 22-05-18
     $dd = Str::of($dateStr)->substr(0,2);
     $mm = Str::of($dateStr)->substr(4,2);
     $yyyy = "20" . Str::of($dateStr)->substr(6,2);

     return "$yyyy-$mm-$dd";
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
     elseif(Str::contains($offering_txt,['initial public offering','ipo'])) {
          return 'IPO';
     }
     elseif(Str::containsAll($offering_txt,['initial public offering','fpo'])) {
          return 'FPO';
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
     public function exportPortfolio(Request $request)
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
               'OTHER',
               'OTHERS',
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
          ];

          $sales = $portfolios->filter(function($item, $key) use($sales_arr){
               return in_array($item['offer_code'], $sales_arr);
          });
          
          //4. insert/update Portfolios table
          if($sales->isNotEmpty()){
               Sales::updateOrCreateSales($sales);
          }

          //5. parse purchase and sales data
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

          //6. delete the data from the meroshare_transactions table          
          $portfolios->each(function($item){
               MeroShare::destroy($item['row_id']);
          });
     
          return response()->json([
               'message' => count($portfolios) . " record(s) have been imported to your portfolio 👌",
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
          //construct collections to store data
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
                         'row_id' => $value->id,
                         'quantity' => empty($value->credit_quantity) ? $value->debit_quantity : $value->credit_quantity,
                         'shareholder_id' => $value->shareholder_id,
                         'symbol' =>  empty($value->share) ? null : $value->share->symbol,
                         'stock_id' =>  empty($value->share) ? null : $value->share->id,
                         'offer_id' =>  empty($value->offer) ? 12 : $value->offer->id,                                       //get from related table; if null 12=Others
                         'offer_code' =>  empty($value->offer) ? 'OTHERS' : $value->offer->offer_code,                        //get from related table
                         'transaction_date' => $value->transaction_date,
                         'remarks' => $value->remarks,
                    );  

                    $portfolios->push( $row );
                    
               };
               
          });

          return $portfolios;

     }

     public function delete(Request $request)
     {
          if( empty($request->trans_id) ){

               return response()->json([
                    'status' => 'error',
                    'message' => 'Confused 👀 Did you select any record at all?',
                ]);

          }

          // /trans_id is comma separated (eg, 1,2,3,4,5), explode into array 
          $ids = Str::of($request->trans_id)->explode(',');
          
          $count  =  MeroShare::whereIn('id', $ids->toArray())->delete();
          $records = $count > 1 ? ' records' : ' record';
          return response()->json([
               'message' => "$count $records deleted. Refreshing the page ⌚ . . .",
               'count' => $count,
          ]);

     }
     
}
