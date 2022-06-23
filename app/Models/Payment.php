<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
 
class Payment extends Model
{

    protected $table = 'App.Payments';
    protected $primaryKey = "Id";
    protected $fillable = ['UserId','OrderId','Amount','PaymentDate','CreatedBy'];
    const CREATED_AT = 'CreationDate';
    const UPDATED_AT = NULL;
   
  
    public function user(){

     return $this->belongsTo(User::class);

    }

    public function order(){

     return $this->belongsTo(Order::class);

    }
        
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
