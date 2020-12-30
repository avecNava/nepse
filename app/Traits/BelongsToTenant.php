<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait  BelongsToTenant
{

    //global scope : to filter records by respective tenant_id
    //https://laravel.com/docs/8.x/eloquent#applying-global-scopes
    protected static function bootBelongsToTenant()                 //boot + name of tenant
    {
        //if the user is logged in
        if(Auth::check()){

            if (session()->get('shareholder_id') != 1) {
                static::addGlobalScope(new TenantScope);
            }

        }

        //or using closure
        // if (auth()->user()->role_id != 1) {
        //     static::addGlobalScope('created_by_user_id', function (Builder $builder) {
        //         $builder->where('created_by_user_id', auth()->id());
        //     });
        // }

        if(session()->has('tenant_id')){
            static::creating(function ($model) {
                $model->tenant_id = session()->get('tenant_id');
            });
        }
        
        
    }

    // public function tenant()
    // {
    //     return $this->belongsTo(Tenant::class);
    // }

}
