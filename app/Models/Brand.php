<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Brand extends Model
 {
    protected $table    = 'App.Brand';
    protected $fillable = ['Id','NameAr','NameEn','BrandStatusId','CreatedBy','UpdatedBy','OrderNo'];
    protected $primaryKey = "Id";

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';

    public function brand()
    {
        return $this->belongsTo(Brand::class)->orderBy('OrderNo');
    }
}
