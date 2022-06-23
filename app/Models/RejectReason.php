<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 

class RejectReason extends Model
{

     protected $table    = 'Lookup.RejectReasons';  
     protected $fillable = ['Name','UserTypeId','CreatedBy','UpdatedBy']; 
     protected $primaryKey = "Id";
     const UPDATED_AT = 'UpdatedDate';
     const CREATED_AT = 'CreationDate';
    


     public function user_type(){

      return $this->belongsTo(UserType::class,'UserTypeId','Id');

     }


 

     // public function orders(){

     //  return $this->hasMany(Order::class,'RegionId','Id');

     // }

     public function scopeDataTable($query)
     {
        //Route::currentRouteName();
        switch (request()->route()->getName()) {
            default:
                return $query->with('user_type');
                break;
        }
    }


 




}