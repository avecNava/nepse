<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyShare;

class MyShareController extends Controller
{

    public function importShares(Request $request)
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
  
                 
                 $transactions->push( 
                      array(
                           'symbol' => $row['Symbol'], 
                           'purchase_date' => $row['Purchase date'],
                           'quantity' => $row['quantity'],
                           'unit_cost' => $row['Unit Cost'],
                           'effective_rate' => $row['Effective rate'],
                           'offer_code' => $row['Offering type'],
                           'shareholder_id' => $shareholder_id,
                           'remarks' => $row['Description'],
                        )
                    );
                });
    
                //remove existing records with the given shareholder_id
                MyShare::where('shareholder_id', $shareholder_id)->delete();

                //add new records
                MyShare::importTransactions($transactions);
    
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
