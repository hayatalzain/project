<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Issue extends Model
 {

    protected $table    = 'App.Issues';
    protected $fillable = ['Id','NameAr','NameEn','IssueStatusId','CreatedBy','UpdatedBy','OrderNo'];
    protected $primaryKey = "Id";
    protected $appends = ['name'];

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';


    public function devicesFav()
    {
        return $this->belongsToMany(issueFavorite::class,'App.UserDevicesFavorites','IssueId','DeviceId')->first();

    }

     public function devices()
     {
          return $this->belongsToMany(Device::class,'App.DeviceIssues','IssueId','DeviceId')->withPivot('MinPrice','MaxPrice','Description','StatusId')->withTimestamps();
     }

    public function getNameAttribute()
     {
        if(session('lang') == 'ar')
        return $this->NameAr;
        return $this->NameEn;
     }


   public function status(){
     return $this->belongsTo(IssueStatus::class,'IssueStatusId');
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
