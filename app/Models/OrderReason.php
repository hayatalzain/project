<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderReason extends Model
 {

    protected $table    = 'App.RejectReasonOrders';
    protected $primaryKey = "Id";
    public $timestamps = false;

    public function reason(){

      return $this->belongsTo(RejectReason::class,'RejectReasonId','Id');

     }

}
