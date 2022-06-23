<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 
class MarketingNotification extends Model
{

    protected $table = 'App.MarketingNotifications'; 
    protected $fillable = ['Title','Message','IsPublished','CreatedBy','UpdatedBy'];
    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';
    protected $primaryKey = "Id";

    protected $casts = ['IsPublished' => 'boolean'];



   public function scopeDataTable($query)
    {

        switch (request()->route()->getName()) {
            default:
                return $query;
                break;
        }
    }
 

}
