<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 

class UserType extends Model
{

      
    protected $table = 'Lookup.UserTypes';
    protected $fillable = ['Name' ];
    protected $primaryKey = "Id";
    public $timestamps = false;
 

     public function scopeDataTable($query)
     {
        //Route::currentRouteName();
        switch (request()->route()->getName()) {
            default:
                return $query->Parent();
                break;
        }
    }


}
