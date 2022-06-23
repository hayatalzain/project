<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Terms extends Model
 {

    protected $table    = 'Lookup.Terms';
    protected $fillable = ['Id','Name','Note','CreatedBy','CreationDate','UpdatedBy','UpdatedDate'];
    protected $primaryKey = "Id";
    public $timestamps = true;

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';

    public function scopeDataTable($query)
    {
        switch (request()->route()->getName()) {
            default:
                return $query;
                break;
        }
    }

}

