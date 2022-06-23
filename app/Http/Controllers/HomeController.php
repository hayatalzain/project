<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Rules\CheckPassword;
use App\Models\Order;
use App\Models\User;
use DB;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    $data['count_orders']        =  Order::count();
    $data['count_orders_today']  =  Order::whereDate('CreationDate',today())->count();
    $data['orders_pending_count']  =  Order::where('OrderStatusId',1)->whereDate('CreationDate',today())->count();
    $data['orders_accepted_count']  =  Order::where('OrderStatusId',2)->whereDate('CreationDate',today())->count();
    $data['orders_cancelled_count']  =  Order::where('OrderStatusId',3)->whereDate('CreationDate',today())->count();
    $data['orders_rejected_count']  =  Order::where('OrderStatusId',6)->whereDate('CreationDate',today())->count();


    $data['technicians_most_orders']  =  User::Technical()->whereHas('transactions',function($q){
      $q->select('UserId')->from('App.Transactions')->where(['OrderStatusId' => 4]);
    })->withCount([ 'orders' => function($q){
       $q->whereIn('Id',function($q){
       $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId' => 4])
       ->whereRaw('[App].[Users].[Id] = UserId');
     });
    }])->orderBy('orders_count','desc')->take(9)->get();



    $order_avg  =  DB::select('SELECT Id,  DATEDIFF(SECOND,AccepteDate.AccepteDate,ExecuteDate.ExecuteDate) / 60 as DiffTime ,  AccepteDate.AccepteDate,ExecuteDate.ExecuteDate
 from App.Orders as orders
 OUTER APPLY (select CreationDate as AccepteDate
 from App.Transactions
 where OrderStatusId = 2
 and OrderId = orders.Id
 group by OrderId,CreationDate
) as AccepteDate
 OUTER APPLY (select CreationDate as ExecuteDate
 from App.Transactions
 where OrderStatusId = 4
 and OrderId = orders.Id
 group by OrderId,CreationDate
) as ExecuteDate
WHERE  ExecuteDate.ExecuteDate IS NOT NULL and AccepteDate.AccepteDate IS NOT NULL

');
      $order_avg_count = collect($order_avg)->count();
      $order_avg_count = $order_avg_count>0 ? $order_avg_count : 1 ;
      $order_avg_sum = collect($order_avg)->sum('DiffTime')/60;
      $data['orders_avg_time']  = round($order_avg_sum / $order_avg_count,2) .' Hour';

     $data['count_technical_online']  =  User::Technical()->where(['IsConnected' => true])->count();

     return view('backend.home',$data);
    }

    public function MyAccount()
    {
        $title = t('Edit My Account');
        return view('backend.my-profile',compact('title'));
    }


    public function UpdateMyAccount()
    {
           $user =  Auth::user();
           $rules = [
            'full_name'    => 'sometimes|max:191',
            'username'     => 'required|unique:sqlsrv.App.admins,username,'.$user->id,
            'email'        => 'required|email|unique:sqlsrv.App.admins,email,'.$user->id,
            'password'     => 'sometimes|nullable|min:6|confirmed',
            'old_password' => ['required_with:password','nullable','min:6',new CheckPassword($user->password)],
         //   'mobile'     => 'required|unique:users,id,'.$user->id,
        ];
     //   dd($this->request->all());
        $data =  $this->request->validate($rules);
        $user->update($data);
        return back()->with('success',t('Successfully Edited'));

    }


   function uploader(){

        $rules = [
            'photo'    => 'required|image|mimes:jpeg,png,jpg,gif|max:6048',
        ];

       $data =  $this->request->validate($rules);

       $path = '/uploads/pages/';
       $destinationPath = public_path($path);
       $photo = $this->request->file('photo');
       $name = 'page_'.str_random(10).uniqid() .'_img.'.$photo->getClientOriginalExtension();
     //  $name = mb_detect_encoding ($name);
       $photo->move($destinationPath, $name);
       return response(['status' => true , 'photo' => url($path.'/'.$name)]);





   }




}
