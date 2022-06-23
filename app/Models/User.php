<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Mail\WelcomeMessage;
use Mail;
use DB;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'App.Users';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'FirstName',
        'LastName',
        'Email',
        'Mobile',
        'Password',
        'IdentityNo',
        'UserStatusId',
        'UserTypeId',
        'AllowNotifications',
        'Comment',
        'CreatedBy',
        'IsSpecial',
       // 'CreationDate',
        'UpdatedBy',
      //  'UpdatedDate'
    ];

    protected $primaryKey = "Id";
    const UPDATED_AT = 'UpdatedDate';
    const CREATED_AT = 'CreationDate';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'Password'
    // ]; //makeVisible('api_token');
// protected $hidden = [
//         'rating_html'
//     ];
    protected $appends = ['full_name','count_invoices','total_invoices','tax_invoices','orders_commision_paid',
        'orders_commision_unpaid','link_profile','rating','rating_html','commission_invoices','region_name',
        'rating_star','count_invoices_test'];


    protected $casts = ['IsConnected' => 'boolean' ];

    public function getTotalInvoicesAttribute()
    {
        return DB::table('App.Orders')->whereIn('Id',function($q){
            $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId'=>4,'UserId'=> $this->Id]);
        })->sum('MaxPrice');
    }

//    public function getRatingAttribute()
//    {
//        return DB::table('App.Rating')->where('TechnicalId',$this->Id)->groupBy('TechnicalId')->avg('Value');
//    }

//test
    public function RatingTest(){
        return $this->hasMany(Rating::class,'TechnicalId');
    }

    public function getRatingAttribute()
    {
        return $this->RatingTest->where('TechnicalId',$this->Id)->avg('Value');
    }

    public function getRatingStarAttribute()
    {
        if($this->UserTypeId == 2){
            return $rating = GetRating($this->rating);
        }
        return '';
    }
    public function getRatingHtmlAttribute($data)
    {
        if($this->UserTypeId == 2){
            return $rating = GetRating($this->rating);
//         return $this->attributes[$rating];
        }
        return '';
    }

    public function getCountInvoicesTestAttribute()
    {
        return DB::table('App.Transactions')->where(['OrderStatusId'=>4,'UserId' => $this->Id])->count();
    }

     public function orders()
     {
          return $this->hasMany(Order::class,'TechnicalId');
     }
     public function user_statuses()
     {
          return $this->belongsTo(UserStatus::class,'UserStatusId');
     }

    public function transactions()
     {
          return $this->hasMany(Transaction::class,'UserId');
     }
     public function favorite()
     {
          return $this->hasMany(Transaction::class,'UserId');
     }

     public function token()
     {
          return $this->hasOne(Token::class,'UserId');
     }

     public function regions()
     {
          return $this->belongsToMany(Region::class,'App.TechnicalRegions','UserId','RegionId');
     }

    public function getFullNameAttribute()
    {
        return $this->FirstName.' '.$this->LastName;
    }


    public function getLinkProfileAttribute()
    {
        return "<a href='".route('users.edit',$this->Id)."'> " . $this->full_name . "</a>";
    }


//    public function getRegionsAttribute()
//    {
//        return
//            dd(DB::table('App.Regions')->where('Id',$this->Id)->groupBy('id'));
//    }

    public function getCountInvoicesAttribute()
    {
        return DB::table('App.Transactions')->where(['OrderStatusId'=>4,'UserId' => $this->Id])->count();
    }


    public function getTaxInvoicesAttribute()
    {
        return ( $this->total_invoices  * 5 ) /  100;
    }
    // UsersController
    public function getCommissionInvoicesAttribute()
    {
//        $count = DB::table('App.Transactions')->where(['OrderStatusId'=>4,'UserId' => $this->Id])->count();
//        $Technical_Commition = Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;
        $Technical_Commition = DB::table('App.Orders')
            ->whereIn('Id',function($q){
                $q->select('OrderId')->from('App.Transactions')->where(['TechnicalId'=> $this->Id,'OrderStatusId' => 4]);
            })->sum('TechnicalCommition');
       return $Technical_Commition ;
    }

    public function getOrdersCommisionPaidAttribute()
    {
        $Technical_Commition = \DB::table('App.Orders')->whereNotNull('PaymentId')->where(['TechnicalId'=>$this->Id])
            ->whereIn('Id',function($q){
                $q->select('OrderId')->from('App.Transactions')->where(['TechnicalId'=> $this->Id,'OrderStatusId' => 4]);
            })->sum('TechnicalCommition');
//        $Technical_Commition = \DB::table('App.Orders')->value('TechnicalCommition');
//        $total1 = $Technical_Commition ;
    return $Technical_Commition;
    }

    public function getRegionNameAttribute()
    {
        $regionId = DB::table('App.TechnicalRegions')->where(['UserId'=>$this->Id])->value('RegionId');
        $regionName = DB::table('App.Regions')->where(['Id'=>$regionId])->value('Name');
        return $regionName;
    }


    public function getOrdersCommisionUnpaidAttribute()
    {
        $Technical_Commition = \DB::table('App.Orders')->whereNull('PaymentId')->where(['TechnicalId'=>$this->Id])
    ->whereIn('Id',function($q){
       $q->select('OrderId')->from('App.Transactions')->where(['TechnicalId'=> $this->Id,'OrderStatusId' => 4]);
    })->sum('TechnicalCommition');
//        $Technical_Commition = \DB::table('App.Orders')->value('TechnicalCommition');
    return $Technical_Commition;
    }


    public function scopeDataTable($query)
     {

     $route = request()->get('route',false);
     $filters = request()->get('filters',[]);
     $state = isset($filters['state'])? $filters['state'] : false;
     $RegionId = request()->get('RegionId',false);

    // \Log::info(json_encode(request()->all()));
    // \Log::info($state.' dd');

     if($route == 'tech.list')
       return $query->whereHas('orders',function($q){
        // $q->whereNotNull('PaymentId');//->where(['OrderStatusId'=> 4]);
       })->when($RegionId,function($q)use($RegionId){
        $q->whereHas('regions',function($q)use($RegionId){
           $q->where('TechnicalRegions.RegionId',$RegionId);
        });
     })->with('regions');


     return $query->when($state,function($q){
        $q->has('token');
     })->when($RegionId,function($q)use($RegionId){
        $q->whereHas('regions',function($q)use($RegionId){
           $q->where('TechnicalRegions.RegionId',$RegionId);
        });
     });

     }

    public function scopeTechnical($query)
     {
       return $query->where('UserTypeId',2);
     }


}
