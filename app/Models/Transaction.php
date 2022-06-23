<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $table = 'App.Transactions';
    protected $dates = ['CreationDate'];



   public function order(){
     return $this->belongsTo(Order::class,'OrderId','Id');
    }

   public function user(){
     return $this->belongsTo(User::class,'UserId','Id')
         ->select('Id','FirstName','LastName','UserStatusId','UserTypeId');
    }

   public function status(){
     return $this->belongsTo(OrderStatus::class,'OrderStatusId');
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
