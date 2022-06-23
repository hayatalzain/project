<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
 
class AdminPermission extends Model
{
    // required to start dataTable integration
    protected $table = 'App.admin_permission';

    protected $fillable = [
        'admin_id',
        'permission_id'
    ];

    public function scopeDataTable($query)
    {
        switch (REFERRER_ROUTE) {
            default :
                return $query;
                break;
        }
    }


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
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