<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Appointments extends Model
 {
    protected $table    = 'App.Appointments';
    protected $fillable = ['Id','Name','DayId','StartTime','EndTime','CreatedBy','UpdatedBy','IsDeleted'];
    protected $primaryKey = "Id";

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';

    public function days(){
        return $this->belongsTo(Days::class,'DayId');
    }

    public function appointments_id(){
        return $this->hasMany("App\Models\Order",'AppointmentId','Id');
    }

}
