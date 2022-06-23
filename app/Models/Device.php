<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{

    protected $table = 'App.device';

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';

     protected $fillable = ['Id','NameAr','NameEn','DeviceStatusId','CreatedBy','UpdatedBy','BrandId','OrderNo'];
     protected $appends = ['name'];

     protected $primaryKey = "Id";

    public function device_issues_Id(){
        return $this->hasMany("App\Models\DeviceIssue",'DeviceId','Id');
    }


    public function brands(){
   return $this->belongsTo(Brand::class,'BrandId')->orderBy('OrderNo');

}


    public function issuesfav()
    {
        return $this->belongsToMany(Issue::class,'App.UserDevicesFavorites','DeviceId','IssueId','UserId')->withPivot('UserId','CreatedBy','CreationDate')->withTimestamps();
    }

     public function orders()
     {
          return $this->hasMany(Order::class);
     }


     public function issues()
     {
          return $this->belongsToMany(Issue::class,'App.DeviceIssues','DeviceId','IssueId')->withPivot('MinPrice','MaxPrice','Description','StatusId')->withTimestamps();
     }


    public function getNameAttribute()
     {
        if(session('lang') == 'ar')
        return $this->NameAr;
        return $this->NameEn;
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
