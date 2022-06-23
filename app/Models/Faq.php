<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Faq extends Model
{

     protected $table    = 'App.Faq';
     protected $fillable = ['Question','Answer','IsActive'];
     protected $primaryKey = "Id";
     public $timestamps = false;

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
