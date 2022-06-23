<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Models\Setting;
use DB;


class OrdersController extends Controller
{
    public function index()
    {
//        $orders =  Order::query()->first();
//
//return $orders ->makeHidden('FirstName');
//    dd($orders);
        if(request()->ajax()){
            $start_date = $this->request->get('FromDate',false);
            $end_date   = $this->request->get('ToDate',false);
            $RegionId   = request()->get('RegionId',false);
            $OrderId    = request()->get('OrderId',false);
            $TechnicalId    = request()->get('TechnicalId',false);
            $OrderStatusId    = request()->get('OrderStatusId',false);

            $orders =  Order::query()->with('customer','technical','transaction','transaction.status','transaction.user','device','issue','status','region')
//                ->join('App.Users', 'App.Orders.TechnicalId' , '=', 'App.Users.Id')

                ->when($start_date,function($q)use($start_date){
                    $q->whereDate('App.Orders.CreationDate','>=',$start_date);
                })->when($end_date,function($q)use($end_date){
                    $q->whereDate('App.Orders.CreationDate','<=',$end_date);

                })      ->when($RegionId,function($q)use($RegionId){
                    $q->where('RegionId',$RegionId);
                })
                ->when($TechnicalId,function($q)use($TechnicalId){
                    $q ->join('App.Users', 'App.Orders.TechnicalId' , '=', 'App.Users.Id')
                    ->where('users.FirstName','LIKE','%'.$TechnicalId.'%' )
                    ->orwhere('users.LastName','LIKE','%'.$TechnicalId.'%' )
                        ->select('orders.*','users.*');
//                    $q->where('TechnicalId',$TechnicalId);
                })
                ->when($OrderStatusId,function($q)use($OrderStatusId){
                    $q->where('OrderStatusId',$OrderStatusId);
                })

                ->when($OrderId,function($q)use($OrderId){
                    $q->where('Code',$OrderId);
                })
                ->when(request()->get('payment',false) == 'paid' , function($q){
                    $q->whereNotNull('PaymentId');
                })->when(request()->get('payment',false) == 'not_paid' , function($q){
                    $q->whereNull('PaymentId');

                });
            return datatables()->of($orders)
                ->rawColumns(['customer.link_profile','technical.link_profile','technical_user.link_profile'])
                ->toJson();
//                ->make(true);
        }

        $title = t('List of Orders');
        $regions = Region::get();
        $technical_users = User::where('UserTypeId','=',2)->get();
//        dd($technical_users->Id);
        return view('backend.orders.index',compact('title','regions','technical_users'));
    }

//    public function index1()
//    {
//        //$date = '2019-10-08';
//        //$orders =Order::whereDate('App.Orders.CreationDate', '>', '2019-10-08')
//        //with('technical')
//        /*with(array('technical'=>function($query){
//                    $query->select('*');
//                }))*/
//            //->whereDate('App.Orders.CreationDate', '>', '2019-10-08')
//            //->whereRaw('App.Orders.CreationDate > 2018-10-08')
//            //->select('technical.App.Users.*')
//            //->technical()->first();
//        //$orders =  Order::query()
//            //->where(DB::raw("App.Orders.CreationDate"),'>','2018-10-08')
//          //  ->join('App.Users', function ($join) {
//          //      $join->on('App.Orders.TechnicalId', '=', 'App.Users.Id');
//           // })
//            //->RightJoin('App.Users', 'App.Orders.TechnicalId' , '=', 'App.Users.Id')
//            //->where(function($query) use ($date) {  // pass in $request
//            //    $query->whereDate('App.Orders.CreationDate','>',$date);
//            //})
//            //->whereDate('App.Orders.CreationDate', '>', '2018-10-08')
//            //->first();
//        //->whereRaw('App.Orders.CreationDate', '>', '2018-10-08')->get();
//          //->whereDate('App.Orders.CreationDate','>','2018-10-08')->get();
//          //  ->whereDate('App.Orders.CreationDate','<=','2019-04-08')->count();
////        $orders =  Order::query()
////            ->join('App.Users', 'App.Orders.TechnicalId' , '=', 'App.Users.Id')
////            //->with(['customer','technical'])
//////            ->value('Code')
////                //->limit(5)
////            ->where('users.FirstName','LIKE','%'.'os'.'%' )
////            ->select('orders.*','users.*')
////            ->get()
////      ->toArray()
////  ;
////dd($orders);
//       if(request()->ajax()){
//        $start_date = $this->request->get('FromDate',false);
//        $end_date   = $this->request->get('ToDate',false);
//        $RegionId   = request()->get('RegionId',false);
//        $OrderId    = request()->get('OrderId',false);
////        $CodeId    = request()->get('Code',false);
//        $TechnicalId    = request()->get('TechnicalId',false);
//        $OrderStatusId    = request()->get('OrderStatusId',false);
//
//        $orders =  Order::query()->with('customer','transaction','technical',
//            'transaction.status','transaction.user','device','issue','status','region')
//
////      ->join('App.Users', 'App.Orders.TechnicalId' , '=', 'App.Users.Id')
//        ->when($start_date,function($q)use($start_date){
//        $q->whereDate('App.Orders.CreationDate','>=',$start_date);
//        })->when($end_date,function($q)use($end_date){
//                $q->whereDate('App.Orders.CreationDate','<=',$end_date);
//            })
////            ->when($TechnicalId ,function($q)use($TechnicalId ){
////                $q->where('users.FirstName','LIKE','%'.$TechnicalId.'%' );
////               // $q->orWhere('users.LastName','LIKE','%'.$TechnicalId.'%' );
////        })
//            ->when($RegionId,function($q)use($RegionId){
//            $q->where('RegionId',$RegionId);
//        })
//            ->when($TechnicalId,function($q)use($TechnicalId){
//            $q->where('TechnicalId',$TechnicalId);
//        })
//            ->when($OrderStatusId,function($q)use($OrderStatusId){
//            $q->where('OrderStatusId',$OrderStatusId);
//        })
//            ->when(!is_null($OrderId),function($q)use($OrderId){
//            $q->where('Code',$OrderId);
//            })
//
//            ->when(request()->get('payment',false) == 'paid' , function($q){
//                $q->whereNotNull('PaymentId');
//            })->when(request()->get('payment',false) == 'not_paid' , function($q){
//                $q->whereNull('PaymentId');
//
//
//        })    ->get()
//      ->toArray();
//
//       return datatables()->of($orders)->rawColumns(['customer.link_profile','technical.link_profile','technical_user.link_profile','technical_user'])
//           ->make(true);
//       }
//        $title = t('List of Orders');
//        $regions = Region::get();
//        return view('backend.orders.index',compact('title','regions'));
//    }

    public function show($id)
    {
        $order =  Order::with(['customer','technical','payment','device','issue',
            'status','color','region','offer','appointments'])
        ->where('Id',$id)->firstOrFail();
        $technicals_obj =  User::Technical()->select('Id','FirstName','LastName')->get();
        $technicals = [];
//        dd($order);
       foreach ($technicals_obj as $technical) {
           $technicals[$technical->Id] = $technical->full_name;
       }
        $title = t('Show Order',['username'=>$order->customer->full_name]);
        return view('backend.orders.form',compact('order','title','technicals'));
    }

    public function History($id)
    {
        $order =  Order::with(['transactions','transactions.user','transactions.status',
            'reason_order','reason_order.reason'])->findOrFail($id);
        $title = t('Order History');
        return view('backend.orders.archive',compact('order','title'));
    }

    public function update($id)
    {
           $rules = [
            'TechnicalId' => 'required',
        ];
        $data =  $this->request->validate($rules);
        $order =  Order::findOrFail($id);
        $order->update($data);
        return redirect()->route('orders')->with('success',t('successfully edited'));
    }

    public function destroy($id)
    {
           $order = Order::find($id);
           $order->payment()->delete();
           $order->delete();
           return redirect()->route('orders')->with('success',t('successfully edited'));
    }

    public function confirmPayment($id)
    {
        $order =  Order::where(['Id'=>$id])->whereNull('PaymentId')->firstOrFail();
  //      $data['Amount'] = Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;
       $data['Amount'] =  $order->TechnicalCommition;
        $data['UserId'] = $order->TechnicalId;
        $data['PaymentDate'] = now();
        $data['CreatedBy'] = auth()->user()->full_name;
        $payment = $order->payment()->create($data);
        $order->PaymentId = $payment->Id;
//        dd($payment);
        $order->save();

        return redirect()->route('orders')->with('success',t('successfully added'));
    }


  public function ExportAllOrders(){

      $start_date = $this->request->get('FromDate',false);
      $end_date   = $this->request->get('ToDate',false);
      $RegionId   = request()->get('RegionId',false);
      $OrderId    = request()->get('Code',false);
//      $CodeId    = request()->get('Code',false);

      $TechnicalId    = request()->get('TechnicalId',false);
      $OrderStatusId    = request()->get('OrderStatusId',false);

      $orders =  Order::query()
          ->with('customer','technical','transaction','transaction.status','transaction.user','device','issue','status','region')

          ->when($start_date,function($q)use($start_date){
              $q->whereDate('App.Orders.CreationDate','>=',$start_date);
          })->when($end_date,function($q)use($end_date){
              $q->whereDate('App.Orders.CreationDate','<=',$end_date);
        })
//          ->when($TechnicalId  ,function($q)use($TechnicalId ){
//              $q->where('FirstName','LIKE','%'.$TechnicalId.'%' );
//              $q->orWhere('LastName','LIKE','%'.$TechnicalId.'%' );
//          })
          ->when($RegionId,function($q)use($RegionId){
              $q->where('RegionId',$RegionId);
          })
          ->when(request()->get('payment',false) == 'paid' , function($q){
              $q->whereNotNull('PaymentId');
          })->when(request()->get('payment',false) == 'not_paid' , function($q){
              $q->whereNull('PaymentId');

          })
          ->when($TechnicalId,function($q)use($TechnicalId){
              $q->where('TechnicalId',$TechnicalId);
          })
          ->when($OrderStatusId,function($q)use($OrderStatusId){
            $q->where('OrderStatusId',$OrderStatusId);
        })
          ->when($OrderId,function($q)use($OrderId){
              $q->where('Code',$OrderId);
          })
          ->when($OrderId,function($q)use($OrderId){
              $q->where('OrderId',$OrderId);
          })
          ->latest('CreationDate')->get();

            // Define the Excel spreadsheet headers
        $order_data[] = ['الكود','العميل','المشكلة','الجهاز' ,'السعر','المنطقة', 'الفني', 'الحالة' ,'حالة السداد','تاريخ التسجيل','تاريخ الموعد','وقت بدء الموعد','وقت انتهاء الموعد'];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($orders as $k => $order) {
            $order_data[++$k]['OrderId']     = $order->Code;
            $order_data[$k]['customer'] = $order->customer->full_name;
            $order_data[$k]['issue']      = $order->issue->name;
            $order_data[$k]['device']     = $order->device->name;
            $order_data[$k]['price']     = $order->MaxPrice.' '.t('SAR');;
            $order_data[$k]['region']     = $order->region->Name;
            $order_data[$k]['technical']  = optional($order->technical)->full_name;
            if($order->transaction and $order->transaction->status){
            $order_data[$k]['status']     = $order->transaction->status->Name;
            if($order->transaction->user)
            $order_data[$k]['status'] .= ' '.t('By').' '.$order->transaction->user->full_name;

          }
            else
            $order_data[$k]['status']        = $order->status->Name;
            $order_data[$k]['payment']       = $order->payment? 'نعم':'لا';
            $order_data[$k]['CreationDate']  = $order->CreationDate;
            $order_data[$k]['AppointmentDate']     = $order->AppointmentDate;
            $order_data[$k]['AppointmentStartTime']     = $order->AppointmentStartTime;
            $order_data[$k]['AppointmentEndTime']     = $order->AppointmentEndTime;
        }
         //  return $order_data;
        // Generate and return the spreadsheet
        \Excel::create('Orders', function($excel) use ($order_data) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Orders');
            $excel->setCreator('Laravel')->setCompany('Halenaha');
            $excel->setDescription('Orders List');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($order_data) {
                $sheet->fromArray($order_data, null, 'A1', false, false);
                $sheet->setRightToLeft(true);
                $sheet->cells('A1:G1', function($cells) {
               /*   $cells->setFontSize(15);
                  $cells->setFontWeight('bold'); */
                 $cells->setFont([
                     'name' => 'arial',
                      'size' => 14,
                     'bold' => true
                 ]);
                 $cells->setBackground('#AAAAFF');
                 $cells->setFontColor("#4d4d4d");
                 $cells->setAlignment('center');
                 $cells->setValignment('center');

                });
            });

        })->download('xlsx');


    }


}
