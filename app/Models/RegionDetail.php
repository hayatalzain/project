<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 

class RegionDetail extends Model
{

     protected $table    = 'App.RegionDetails';  
     protected $fillable = ['Name','Lat','Long','Radius','RegionId','CreatedBy','UpdatedBy','IsActive']; 
     protected $primaryKey = "Id";
     const UPDATED_AT = 'UpdatedDate';
     const CREATED_AT = 'CreationDate';
    



     public function region(){

      return $this->belongsTo(Region::class,'RegionId','Id');

     }

     public function scopeDataTable($query)
     {
        //Route::currentRouteName();
        switch (request()->route()->getName()) {
            default:
                return $query->with('region');
                break;
        }
    }




    public function toArray()
    {
        $attributes = parent::toArray();
        $attributes['Radius'] = round($attributes['Radius'],1);

        return $attributes;
    }




}