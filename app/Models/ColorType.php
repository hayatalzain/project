<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ColorType extends Model
{


    protected $table = 'Lookup.ColorTypes';
    protected $fillable = ['Name','Status','OrderNo'];
    protected $primaryKey = "Id";
    public $timestamps = false;

    public function color_type_Id(){
        return $this->hasMany("App\Models\Order",'ColorTypeId','Id');
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
