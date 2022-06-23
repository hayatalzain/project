<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
 
class Rating extends Model
{

    protected $table    = 'App.Rating';
    protected $primaryKey = "Id";
    protected $fillable = ['Value','Comment','CustomerId','TechnicalId','OrderId','CreatedBy','UpdatedBy'];
    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';
   

    public function customer(){
     return $this->belongsTo(User::class,'CustomerId');
    }

    public function technical(){
     return $this->belongsTo(User::class,'TechnicalId');
    }

    public function order(){
     return $this->belongsTo(Order::class,'OrderId');
    }
 
        
     public function scopeDataTable($query)
     {
        //Route::currentRouteName();
        switch (request()->route()->getName()) {
            default:
                return $query->with('user');
                break;
        }
    }



  



}
