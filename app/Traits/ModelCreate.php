<?php

namespace App\Traits;

trait  ModelCreate
{

    /**
     * adds the last_modified_by to the models
     */
    protected static function bootModelCreate()
    {
        
        if(session()->has('user_id')){
            static::creating(function ($model) {
                $model->last_modified_by = session()->get('user_id');
            });
        }
        
        
    }

}
