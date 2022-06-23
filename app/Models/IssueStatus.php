<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 
class IssueStatus extends Model
 {

    protected $table    = 'Lookup.IssueStatuses';
    protected $fillable = ['Name' ];
    protected $primaryKey = "Id";
    public $timestamps = false;
 

}
