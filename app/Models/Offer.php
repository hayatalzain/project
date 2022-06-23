<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 

class Offer extends Model
{

     protected $table    = 'App.Offers';  
     protected $fillable = ['Name','Code','DiscountPercent','CreatedBy','UpdatedBy','IsActive']; 
     protected $primaryKey = "Id";
     const UPDATED_AT = 'UpdatedDate';
     const CREATED_AT = 'CreationDate';
    

     public function scopeDataTable($query)
     {
        //Route::currentRouteName();
        switch (request()->route()->getName()) {
            default:
                return $query;
                break;
        }
    }




}
