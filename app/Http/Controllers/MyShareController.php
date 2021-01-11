<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyShare;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use App\Models\Shareholder;
use Illuminate\Support\Str;
use App\Services\UtilityService;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MyShareController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']); 
    }

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
         $transactions = MyShare::where('shareholder_id', $shareholder_id)
                         ->with(['share'])
                         ->get();
 
         return view('import.myshare', [
                 'transactions' => $transactions,
                 'shareholders' => $shareholders,
         ]);
     }
 
    public function store(Request $request)
    {
          $validator = $request->validate([
               'shareholder' => 'required',
               'file' => 'required'
          //  'file' => 'required|mimes:xls,xlsx'
          ]);

          // $destinationPath = base_path('public/menu');
          $destinationPath = storage_path('app/meroshare');
          $file_name = $request->file('file')->getClientOriginalName();
          //   $extension = $request->file('file')->extension();      //retuns txt for html , sql files...

          //get the file extension from filename manually
          $tmp = explode('.', $file_name);
          $extension = $tmp[ sizeof($tmp)-1 ];

          //   // Valid File Extensions
          $valid_extension = ["csv","xlsx"];

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
                    $transactions->push( 
                         array(
                              'symbol' => $row['Symbol'], 
                              'purchase_date' => $row['Purchase date'],
                              'quantity' => $row['Quantity'],
                              'unit_cost' => $row['Unit Cost'],
                              'effective_rate' => $row['Effective rate'],
                              'offer_code' => $row['Offering type'],
                              'shareholder_id' => $shareholder_id,
                              'description' => $row['Remarks']
                              )
                         );
               });
               
          } catch (\Throwable $th) {
               $error = 
                    [
                         'message' => $th->getMessage(),
                         'line' => $th->getLine(),
                         'file' => $th->getFile(),
                    ];
               Log::error('Import error',$error);
               return redirect()->back()->with('error', "ERRROR : " . $error['message']);
          }
          
          try {
               \File::delete( $pathToCSV );
               // unlink( $pathToCSV );               //delete the file
          } catch(\Throwable $th){

          }
          
          //remove existing records with the given shareholder_id
          MyShare::where('shareholder_id', $shareholder_id)->delete();
          
          //add new records
          $success = MyShare::importTransactions($transactions);

          //get uuid for the shareholder
          $user = Shareholder::find( $request->input('shareholder'));

          if(!$success){
               return redirect("import/share/" . $user->uuid)->with('error', "Import completed. <br>Unfortunately, some of the records could not be imported 👀");
          }
          
          return redirect('states/'.$id.'/regions')->with('success', 'Records imported successfully 👌 <br/>From the shareholder below, choose the records and click "Save to Portfolio');

    }

    public function exportPortfolio(Request $request)
    {

          if( empty($request->trans_id) ){
               return response()->json([
                    'status' => 'error',
                    'message' => 'Confused 👀 Did you select any record at all?',
               ]);
          }

          //trans_id is comma separated (eg, 1,2,3,4,5), explode into array 
          $ids = Str::of($request->trans_id)->explode(',');
          
          // Get portfolio from my_shares table, related data from Shares and Offers table 
          $portfolios = 
               MyShare::whereIn('id', $ids->toArray())
               ->with(['share:id,symbol','offer:id,offer_code'])         //relationships (share, offer)
               ->get();
          
          //if IPO, unit cost and effective rate = 100, BONUS,it will be 0, total_amount is qty*effective_rate
          // $offers =['IPO','FPO','RIGHTS','BONUS','MUTUALFUND','PREMIUM','SECONDARY','OTC','AUCION','BONDS','BOND','OTHER','OTHERS'];

          $portfolios->each(function($item){
               
               Portfolio::updateOrCreate(
               [
                    'stock_id' =>  $item->share->id,
                    'shareholder_id' => $item->shareholder_id,
                    'quantity' => $item->quantity, 
                    'purchase_date' => $item->purchase_date,
                    'tags' => "import",
               ],
               [
                    'offer_id' => !empty($item->offer) ?: $item->offer->id,
                    'last_modified_by' => Auth::id(),
                    'remarks' => $item->symbol . ' ' . $item->description,
                    'unit_cost' => $item->unit_cost,
                    'effective_rate' => $item->effective_rate,
                    'total_amount' => $item->effective_rate ?: round($item->quantity * $item->effective_rate, 2),
                    'wacc_updated_at' => Carbon::now(),
                    'wacc_updated_at' => empty($item->effective_rate) ? null :  Carbon::now(),
                    // 'wacc_updated_at' => in_array(Str::upper($item->offer_code), $offers) ? Carbon::now() : null,

          ]);

          //replaced the add logic with updateOrCreate (above)
          // $portfolio = new Portfolio();
          // $portfolio->shareholder_id = $item->shareholder_id;
          // $portfolio->stock_id =  empty($item->share) ? null : $item->share->id;
          // $portfolio->quantity = $item->quantity;
          // $portfolio->unit_cost =  $item->unit_cost;
          // $portfolio->effective_rate =  $item>effective_rate;
          // $portfolio->offer_id =  empty($item->offer) ? null : $item->offer->id;
          // $portfolio->purchase_date = $item->purchase_date;
          // $portfolio->last_modified_by = Auth::id();
          // $portfolio->remarks = $item->description;
          // $portfolio->tags = 'import';          
          // $portfolio->save();
               
          });
       
          //insert/update portfolio summary table with the aggregated data
          $portfolios->each(function($item){
               PortfolioSummary::updateCascadePortfoliSummaries($item->shareholder_id, $item->share->id);
          });
     
          return response()->json([
               'message' => count($portfolios) . " records have been imported to your portfolio 👌",
               'count' => count($portfolios),
          ]);

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

        $count  =  MyShare::whereIn('id', $ids->toArray())->delete();
        $records = $count > 1 ? ' records' : ' record';
        return response()->json([
                'message' => "$count $records deleted. Refreshing the page ⌚ . . .",
                'count' => $count,
            ]);

    }
    
}
