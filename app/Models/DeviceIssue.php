<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DeviceIssue extends Model
 {

    protected $primaryKey = "Id";
    protected $table    = 'App.DeviceIssues';
    protected $fillable = [
        'DeviceId',
        'IssueId',
        'MinPrice',
        'MaxPrice',
        'Description',
        'StatusId',
        'CreatedBy',
        'UpdatedBy',
    ];

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';
    protected $appends = ['brand_append'];

    public function device(){
     return $this->belongsTo(Device::class,'DeviceId');
    }

   public function issue(){
     return $this->belongsTo(Issue::class,'IssueId');
    }
    public function getBrandAppendAttribute(){
        return  \App\Models\Device::with('brands')->select('BrandId')->first()->BrandId;

    }

}
