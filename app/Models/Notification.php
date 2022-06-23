<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{

     public $incrementing = false;
     protected $table    = 'App.Notifications';
     protected $fillable = ['id','type','notifiable_id','notifiable_type','data','read_at','created_at'];
     protected $with     = ['recipient'];
     protected $appends  = ['seen'];
     protected $casts    = ['data' => 'array'];


    public function recipient(){

     return $this->belongsTo(User::class,'notifiable_id');

    }

    public function getSeenAttribute(){

     return $this->read_at;

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

    public function scopeNotSeen($query){

     return $query->whereNull('read_at');
    }

}
