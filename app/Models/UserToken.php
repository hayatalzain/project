<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 
class UserToken extends Model
 {

   
    protected $table    = 'App.Tokens';
    protected $fillable = [
        'UserId',
        'TokenId',
        'CreatedBy',
    ];
    const UPDATED_AT = null;
    const CREATED_AT = 'CreationDate';


   public function user(){
     return $this->belongsTo(User::class,'UserId');
    }   

}
