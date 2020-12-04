<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Services\UtilityService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\MeroShare;
use App\Models\StockOffer;
use App\Models\Shareholder;
use Illuminate\Support\Facades\Auth;
use Spatie\SimpleExcel\SimpleExcelReader;


class MeroShareController extends Controller
{
   public function index()
   {
        
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
   public function importTransaction(Request $request)
   {
          $validator = $request->validate([
               'shareholder' => 'required',
               'file' => 'required'
          //   'file' => 'required'
          ]);

          // $destinationPath = base_path('public/menu');
          $destinationPath = storage_path('app/meroshare');
          $file_name = $request->file('file')->getClientOriginalName();
          $extension = $request->file('file')->extension();

          $new_name = UtilityService::serializeTime() .'-'. UtilityService::serializeString($file_name);
          $request->file('file')->move($destinationPath, $new_name);
          $pathToCSV = $destinationPath .'/'. $new_name;
          
          // Valid File Extensions
          $valid_extension = ["txt","csv","xls","xlsx"];

          // Check file extension
          if( false == in_array(strtolower($extension),$valid_extension)){
          return redirect()->back()->with('error','File type not supported. Please provide an XLSX or a CSV file');   
          }
          
          
          $transactions = collect();
          $shareholder_id = $request->input('shareholder');
          $rows = SimpleExcelReader::create($pathToCSV)->getRows();        // $rows is an instance of Illuminate\Support\LazyCollection

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

          return redirect()->back()->with('success', 'The transactions has been imported successfully.');   
        
   }

   public static function getOfferType($str){
     
     $offering_txt = Str::lower($str);

     if(Str::contains($offering_txt,'ca-bonus')){
          return 'BONUS';
     }
     elseif(Str::contains($offering_txt,'ca-rights')){
          return 'RIGHTS';
     }
     elseif(Str::containsAll($offering_txt,['initial public offering','fpo'])) {
          return 'IPO';
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
}
