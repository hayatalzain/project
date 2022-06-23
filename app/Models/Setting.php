<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 

class Setting extends Model
{

    protected $table = 'dbo.Settings'; 
	protected $fillable = [
		'SettingKey',
		'SettingValue',
	];

    const UPDATED_AT = NULL;
    const CREATED_AT = NULL;
//    protected $hidden  = ['id','created_at','updated_at'];
 

}
