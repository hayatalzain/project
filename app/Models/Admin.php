<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminResetPasswordNotification;

class Admin extends Authenticatable
{
    use Notifiable;


    protected $table = 'App.admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'api_token',
        'fcm_token',
        'status',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $appends = ['roles'];

    public function setPasswordAttribute($value)
    {
        if(!is_null($value)){
        if( \Hash::needsRehash($value) ) {
            $value = bcrypt($value);
        }
        $this->attributes['password'] = $value;
      }
    }



    public function scopeDataTable($query)
     {

       switch (request()->route()->getName()) {
            default:
                return $query;
                break;
        }
     }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }


    public function permissions()
    {
      return $this->belongsToMany(Permission::class,'App.admin_permission');
    }

    public function hasPermission($permission)
    {
    if($this->permissions()->where('role', $permission)->first())
    {
        return true;
    }
    else
    {
        return false;
    }

   }

    public function getRolesAttribute()
    {
      return $this->permissions->pluck('role')->toArray();
    }




}
