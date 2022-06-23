<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class Permission extends Model

{
    protected $table = 'App.permissions';

    protected $fillable = [
        'name',
        'role'
    ];


    public function scopeDataTable($query)
    {
        switch (REFERRER_ROUTE) {
            default :
                return $query;
                break;
        }
    }


   public function admins(){

    return $this->belongsToMany(Admin::class,'admin_permission','permission_id','admin_id')->withTimestamps();

   }


    public function toArray()
    {
        $array = parent::toArray();

        // required to filter datatable
        if (!defined('REFERRER_ROUTE')) {
            return $array;
        }

        return $array;
    }
}
