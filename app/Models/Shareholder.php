<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToTenant;
use App\Services\UtilityService;
use Illuminate\Support\Facades\DB;

class Shareholder extends Model
{
    use HasFactory, BelongsToTenant;
    
    protected $fillable = [
        'first_name',
        'last_name',
        'parent_id',
        'parent',
        'relation',
        'gender',
        'email',
        'date_of_birth',
        'last_modified_by',
        'tenant_id',
        'uuid',
    ];

    public function scripts()
    {
        return $this->hasMany('App\Models\PortfolioSummary', 'shareholder_id');
    }
    
    public function portfolio()
    {
        return $this->hasMany('App\Models\Portfolio', 'shareholder_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');        //join Shareholder and user by parent_id and  ids
    }

    public static function shareholdersWithPortfolio($user_id, $uuid = FALSE)
    {
        
        $parent = $user_id;

        //if the given id is uuid then, get the id first
        if($uuid){
            $parent = Shareholder::where('uuid', $user_id)
                ->pluck('parent_id')->first();
        }

        return  DB::table('shareholders as s')
            ->join('portfolio_summaries as p','s.id','p.shareholder_id')
            ->where('s.parent_id', $parent)
            ->selectRaw('DISTINCT(p.shareholder_id),first_name,last_name,uuid')
            ->get();
    }
    
    /**
     * shareholders with at least one sales
     */
    public function sale()
    {
        return $this->hasOne('App\Models\Sales', 'shareholder_id');
    }

    public static function getShareholderUUID($id)
    {
        $temp = Shareholder::find($id);
        return optional($temp)->uuid;
    }
    public static function getShareholderName($id)
    {
        $temp = Shareholder::find($id);
        return optional($temp)->first_name . ' ' . optional($temp)->last_name;
    }

    public static function getShareholderDetail($uuid)
    {
        return Shareholder::where('uuid', $uuid)->first();
    }

    public static function getShareholderNames($parent_id)
    {
        $shareholders = Shareholder::where('parent_id', $parent_id)->get();
        $shareholder = $shareholders->map(function($item, $key){
            return collect([
                'name' => "$item->first_name $item->last_name",
                '_name' => UtilityService::serializeString("$item->first_name $item->last_name","-"),
                'relationF' => !empty($item->relation) ? "($item->relation)":'(Self)',
                'relation' => $item->relation, 
                'date_of_birth' => $item->date_of_birth,
                'gender' => $item->gender,
                'email' => $item->email,
                'id' => $item->id,
                'uuid' => $item->uuid,
            ]);

        });
        return $shareholder;
    }
    /**
     * shareholders with at least one item in cart
     */
    public function cart()
    {
        return $this->hasOne('App\Models\SalesBasket', 'shareholder_id');
    }

    public static function createShareholder(Request $request)
    {
        Shareholder::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'last_modified_by' => Auth::id(),
                'parent_id' => Auth::id(),
                'first_name' => Str::title($request->first_name),
                'last_name' => Str::title($request->last_name),
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => Str::title(Str::substr($request->gender,0,1)),
                'relation' => (Auth::id() == $request->id) ? null : Str::title($request->relation), //relation is null for main a/c holders
                'parent' => false,
                'uuid' => Str::uuid(),
            ]
        );
    }
  
    /**
     * creates sample records for the logged in user 
     * Groups are also considered as shareholders 
     */
    public static function createSampleRecord()
    {
        Shareholder::updateOrCreate(
            [
            'last_modified_by' => Auth::id(),
            'parent_id' => Auth::id(),
            'first_name' => 'Long',
            'last_name' => 'term',
            'relation' => 'Group',
            'parent' => false,
            'uuid' => Str::uuid(),
        ]);
            
        Shareholder::updateOrCreate([
            'last_modified_by' => Auth::id(),
            'parent_id' => Auth::id(),
            'first_name' => 'Short',
            'last_name' => 'term',
            'relation' => 'Group',
            'parent' => false,
            'uuid' => Str::uuid(),
        ]);

        Log::info('Created shareholder groups (long/short term)', [optional(Auth::user())->name]);
    }
    
    /**
     * returns Shareholders with Sales record
     */
    public static function shareholdersWithSales($parent_id)
    {
        $shareholders = Shareholder::where('parent_id', $parent_id)
            ->with('sale')
            ->get();

        $shareholder = $shareholders->map(function($item, $key){
            return collect([
                'name' => "$item->first_name $item->last_name",
                'gender' => $item->gender,
                'relation' => $item->relation,
                'id' => $item->id,
                'uuid' => $item->uuid,
            ]);

        });
        return $shareholder;
    }
    
    /**
     * returns Shareholders with Carts record
     */
    public static function shareholdersWithCarts($parent_id)
    {
        $shareholders = Shareholder::where('parent_id', $parent_id)
            ->with('cart')
            ->get();

        $shareholder = $shareholders->map(function($item, $key){
            return collect([
                'name' => "$item->first_name $item->last_name",
                'gender' => $item->gender,
                'relation' => $item->relation,
                'id' => $item->id,
                'uuid' => $item->uuid,
            ]);

        });
        return $shareholder;
    }

    public static function getShareholderIds($user_id)
    {
        $records = Shareholder::where('parent_id', $user_id)->select('id')->get();
        $shareholders = $records->map(function($item){
            return $item->id;
        });

        return $shareholders;
    }
}
