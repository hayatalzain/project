<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 

class Region extends Model
{

     protected $table    = 'App.Regions';  
     protected $fillable = ['Name','CreatedBy','UpdatedBy','IsActive']; 
     protected $primaryKey = "Id";
     const UPDATED_AT = 'UpdatedDate';
     const CREATED_AT = 'CreationDate';
    



     public function regions_details(){

      return $this->hasMany(RegionDetail::class,'RegionId');

     }

     public function scopeDataTable($query)
     {
        //Route::currentRouteName();
        switch (request()->route()->getName()) {
            default:
                return $query->withCount('regions_details');
                break;
        }
    }




}
