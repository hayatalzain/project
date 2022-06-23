<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 
class PaymentStatus extends Model
 {

   
    protected $table    = 'Lookup.PaymentStatuses';
    protected $fillable = ['Name' ];
    protected $primaryKey = "Id";
    public $timestamps = false;
 

}
