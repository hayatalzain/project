<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 
class DeviceStatus extends Model
 {

   
    protected $table    = 'Lookup.DeviceStatuses';
    protected $fillable = ['Name' ];
    protected $primaryKey = "Id";
    public $timestamps = false;
 

}
