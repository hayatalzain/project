<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevicesFavorites extends Model
{

    protected $table = 'App.UserDevicesFavorites';

    const UPDATED_AT = null;
    const CREATED_AT = 'CreationDate';

     protected $fillable = ['Id','UserId','DeviceId','IssueId','CreatedBy','CreationDate'];
      protected $hidden = ['result'];
//     protected $appends = ['name'];
//$result

     protected $primaryKey = "Id";



    public function deviceFavorite(){
        return $this->belongsTo(Device::class,'DeviceId');

    }

    public function issueFavorite(){
        return $this->belongsTo(Issue::class,'IssueId');
    }
    public function userFavorite(){
        return $this->belongsTo(User::class,'UserId');
    }

    public function scopeDataTable($query)
     {
         switch (request()->route()->getName()) {
            default:
                return $query;
                break;
        }
     }

}
