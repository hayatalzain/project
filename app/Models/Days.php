<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Days extends Model
 {
    protected $table    = 'App.Days';
    protected $fillable = ['Id','NameAr','NameEn','CreatedBy','UpdatedBy'];
    protected $primaryKey = "Id";

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';
}
