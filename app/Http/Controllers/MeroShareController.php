<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Services\UtilityService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelReader;


class MeroShareController extends Controller
{
   public function index()
   {
        
   }

   public function importTransactionForm()
   {
        return view('meroshare.import-transaction');
   }
   
   public function importTransaction(Request $request)
   {
          $validator = $request->validate([
               'file' => 'required|mimes:csv,xlsx,ods'
          //   'file' => 'required'
          ]);

          // $destinationPath = base_path('public/menu');
          $destinationPath = storage_path('app/meroshare');
          $file_name = $request->file('file')->getClientOriginalName();
          $new_name = UtilityService::serializeTime() .'-'. UtilityService::serializeString($file_name);
          $request->file('file')->move($destinationPath, $new_name);
          $pathToCSV = $destinationPath .'/'. $new_name;
          
          // Valid File Extensions
          //    $valid_extension = ["csv","xls","xlsx"];

          //    // Check file extension
          //    if( false == in_array(strtolower($extension),$valid_extension)){
          //      return redirect()->back()->with('error','File type not supported. Please provide an XLSX or a CSV file');   
          //    }
          
          
          // $rows is an instance of Illuminate\Support\LazyCollection
          $rows = SimpleExcelReader::create($pathToCSV)->getRows();
          $trans = collect();

          $rows->each(function(array $row) use ($trans) {
               $remarks = $row['History Description'];
               $trans->push( 
                    array(
                         'symbol' => $row['Scrip'], 
                         'transaction_date' => $row['Transaction Date'],
                         'credit_quantity' => Str::of( $row['Credit Quantity'] )->contains('-') ? null : $row['Credit Quantity'],
                         'debit_quantity' => Str::of( $row['Debit Quantity'] )->contains('-') ? null : $row['Debit Quantity'],
                         'transaction_mode' => $this->getTransactionMode($remarks),
                         'offering_type' => $this->getOfferingType($remarks),
                         'remarks' => $remarks
                    )
               );
               // array_merge($trans, $temp);
          });
          dd($trans);
          return redirect()->back()->with('success', 'The transactions has been imported successfully.');   
        
   }

   public static function getOfferingType($str){
     
     $offering_txt = Str::lower($str);

     if(Str::contains($offering_txt,'ca-bonus')){
          return 'BONUS';
     }
     elseif(Str::contains($offering_txt,'ca-rights')){
          return 'RIGHTS';
     }
     elseif(Str::containsAll($offering_txt,['initial public offering','ipo'])) {
          return 'IPO';
     }
     elseif(Str::containsAll($offering_txt,['initial public offering','fpo'])) {
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
          return 'UNKNOWN';
     }
     
   }

   public static function getTransactionMode($str){
     
     $txt = Str::lower($str);

     if(Str::contains($txt,'on-dr td')){
          return 'DR';
     }
     elseif(Str::contains($txt,['on-cr td','cr current balance','credit'])) {
          return 'CR';
     }
     else {
          return 'NA';
     }
     
   }
}
