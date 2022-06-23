<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 
class UserStatus extends Model
 {

   
    protected $table    = 'Lookup.UserStatuses';
    protected $fillable = ['Name' ];
    protected $primaryKey = "Id";
    public $timestamps = false;
 
 

}
