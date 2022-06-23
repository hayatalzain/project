<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'App.Orders';
    protected $fillable = ['Code','UserId','RequestDate','DeviceId','IssueId','Comment','Lat',
        'Long','GoogleAddress','OrderStatusId','ColorTypeId','CreatedBy','UpdatedBy','MinPrice',
        'MaxPrice','full_name','PaymentId','AppointmentId','AppointmentDate','AppointmentEndTime',
        'AppointmentStartTime','TechnicalCommition'];

    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';
    protected $primaryKey = "Id";
    protected $dates = ['RequestDate'];
    protected $appends = ['tax','commission','technical_user'];
    protected $hidden = ['RowVersion'];

    public function customer(){
     return $this->belongsTo(User::class,'UserId');
    }
    public function appointments(){
     return $this->belongsTo(Appointments::class,'AppointmentId');
    }

    public function technical(){
     return $this->belongsTo(User::class,'TechnicalId');
    }

    public function payment(){
     return $this->hasOne(Payment::class,'OrderId');
    }

    public function transactions(){
     return $this->hasMany(Transaction::class,'OrderId');
    }

    public function transaction(){
     return $this->hasOne(Transaction::class,'OrderId')->latest('CreationDate');
    }

   public function device(){
     return $this->belongsTo(Device::class,'DeviceId');
    }

   public function issue(){
     return $this->belongsTo(Issue::class,'IssueId');
    }

   public function status(){
     return $this->belongsTo(OrderStatus::class,'OrderStatusId');
    }

   public function reason_order(){
     return $this->hasOne(OrderReason::class,'OrderId');
    }

   public function color(){
     return $this->belongsTo(ColorType::class,'ColorTypeId');
    }

    public function region(){
     return $this->belongsTo(Region::class,'RegionId');
    }

    public function offer(){
     return $this->belongsTo(Offer::class,'OfferId');
    }

   public function getTaxAttribute(){
     return ($this->MaxPrice * 5 ) / 100;
    }

    public function getCommissionAttribute(){
//     return  Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;
    }
    public function getTechnicalUserAttribute(){

        if($this->technical)
            return $this->technical;
        return \App\Models\User::where('UserTypeId',2)->whereIn('Id',function($q){
            $q->select('UserId')->from('App.Transactions')->where('OrderId',$this->Id);
        })->first();

    }

//   public function getTechnicalNameAttribute(){
//
//    if($this->technical)
//    return $this->technical;
//   return \App\Models\User::where('UserTypeId',2)->whereIn('Id',function($q){
//       $q->select('UserId')->from('App.Transactions')->where('OrderId',$this->Id);
//     })
////       ->value('FirstName')
//      ->first();
////       $tech = \App\Models\User::where('UserTypeId',2)->whereIn('Id',function($q){
////           $q->select('UserId')->from('App.Transactions')->where('OrderId',$this->Id);
////       })->first();
////       return $tech->FirstName.' '.$tech->LastName;
////       if(isset($tech->FirstName)){
////           return $tech->FirstName.' '.$tech->LastName;
////       }
//    }


   // public function scopeDataTable($query)
   //  {

   //   $route = request()->get('route',false);
   //   $search = request()->get('search',[]);
   //   $term = isset($search['value'])? $search['value'] : false;
   //   $state = isset($filters['state'])? $search['state'] : false;


   //      switch (request()->route()->getName()) {
   //          default:
   //              return $query->with(['customer','technical','transaction','transaction.status','transaction.user','payment','device','issue','status','color','region'])
   //              ->when($term,function($q)use($term){
   //              $q->WhereHas('device',function($q)use($term){
   //                 $q->Where('NameAr','like','%'.$term.'%');
   //                 $q->OrWhere('NameEn','like','%'.$term.'%');
   //              });

   //              });
   //              break;
   //      }
   //  }


}
