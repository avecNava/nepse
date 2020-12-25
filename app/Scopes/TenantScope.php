<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

//global scope:  filter records by respective tenant_id
//https://laravel.com/docs/8.x/eloquent#global-scopes

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        info('Global scope', [$model]);
        
        if(session()->has('tenant_id')){
            $builder->where('tenant_id', session()->get('tenant_id'));
        }
    }
}