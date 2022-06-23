<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderStatus extends Model
 {

    protected $table    = 'Lookup.OrderStatuses';
    protected $fillable = ['Name' ];
    protected $primaryKey = "Id";
    public $timestamps = false;

}
