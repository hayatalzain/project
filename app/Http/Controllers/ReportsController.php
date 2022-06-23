<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use App\Models\Region;
use App\Models\OrderStatus;
use DB;

class ReportsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNumberOrdersByDate()
    {

    $date = $this->request->get('date',today()->format('Y-m-d'));
    $count_orders_today  =  Order::whereDate('CreationDate',$date)->count();

     return response($count_orders_today);
    }


    public function getMostRepairedDevices()
    {
       $DeviceName = 'NameEn';
       $IssueName  = 'NameEn';

      if(isRtl()){
       $DeviceName = 'NameAr';
       $IssueName  = 'NameEn';
       }


    $devices  =  DB::select('
select TOP 7 d.'.$DeviceName.' as DeviceName,
       o.count_devices
     , i.IssueName
     , o.IssueId
from App.Device  as d
inner join
(
    select DeviceId,IssueId, count(Id) as count_devices
    from App.Orders
    group by DeviceId,IssueId
) as o
    on d.Id = o.DeviceId
inner join
(
    select Id,'.$IssueName .' IssueName
    from App.Issues
) as i
    on i.Id = o.IssueId
    order by count_devices desc
');

     return response($devices);
    }


    public function getFinancial()
    {
//        $total_commission = Order::query()->get()->take(10);

//      $orders =  Order::query()->with('customer','technical','transaction','payment','device','issue','status','color')->first();
//        $total_commission  = round($Technical_Commition *  $orders->count());
//      dd($total_commission);
    if(request()->ajax()){

        $start_date = $this->request->get('start_date',false);
        $end_date   = $this->request->get('end_date',false);
        $RegionId   = $this->request->get('RegionId',false);
        $OrderStatusId  = $this->request->get('OrderStatusId',false);
        $orders =  Order::query()
            ->with('customer','technical','transaction','payment','device','issue','status')
            ->when($start_date,function($q)use($start_date){
                $q->whereDate('App.Orders.CreationDate','>=',$start_date);
            })->when($end_date,function($q)use($end_date){
                $q->whereDate('App.Orders.CreationDate','<=',$end_date);
            })
            ->when($RegionId,function($q)use($RegionId){
            $q->where('RegionId',$RegionId);
            })
            ->when($OrderStatusId,function($q)use($OrderStatusId){
                $q->where('OrderStatusId',$OrderStatusId);
        })
            ->when(request()->get('payment',false) == 'paid' , function($q){
                $q->whereNotNull('PaymentId');
             })->when(request()->get('payment',false) == 'not_paid' , function($q){
                $q->whereNull('PaymentId');
             });
//            ->latest('CreationDate')
//            ->get();

      $total_invoices = round($orders->sum('MaxPrice'));
      $total_tax = round(($total_invoices * 5 ) / 100);

//      $total_commission  = round('TechnicalCommition' *  $orders->count());
       $total_commission  = round($orders->sum('TechnicalCommition') );

     return datatables()->of($orders)->with('total_invoices',$total_invoices)
         ->with('total_tax',$total_tax)
         ->with('total_commission',$total_commission)
         ->rawColumns(['customer.link_profile','technical.link_profile'])
//      ->make(true)
         ->toJson();
    }
    $regions = Region::get();
    $OrderStatus = OrderStatus::get();
    $title   = t('Financial Report');
    return view('backend.reports.financial',compact('title','regions','OrderStatus'));
    }



public function ExportReportsFinancial(){

    $start_date = request()->get('start_date',false);
    $end_date   = request()->get('end_date',false);
    $RegionId   = request()->get('RegionId',false);
    $OrderStatusId  = request()->get('OrderStatusId',false);

//      $from_date    = $this->request->get('from_date',false);
//      $to_date      = $this->request->get('to_date',false);
//      $region_id    = $this->request->get('RegionId',false);
//      $search       = $this->request->get('q','');
//      $model = new Order;
//      $valid_columns = $model->getFillable();

        $orders =  Order::query()
            ->with('customer','technical','transaction','transaction.status','transaction.user','device','issue','status','region','payment','color')

//        Where(function($q)use($valid_columns,$search){
//            foreach($valid_columns as $i => $field){
//            if($i==0)
//            $q->where($field, 'like', "%" . $search . "%");
//            else
//            $q->OrWhere($field, 'like', "%" . $search . "%");
//            }
//        })

            ->when($start_date,function($q)use($start_date){
                $q->whereDate('App.Orders.CreationDate','>=',$start_date);
            })->when($end_date,function($q)use($end_date){
                $q->whereDate('App.Orders.CreationDate','<=',$end_date);
            })

            ->when($RegionId,function($q)use($RegionId){
                $q->where('RegionId',$RegionId);
            })
            ->when($OrderStatusId,function($q)use($OrderStatusId){
                $q->where('OrderStatusId',$OrderStatusId);
//    الحالة
            })
            ->when(request()->get('payment',false) == 'paid' , function($q){
      $q->whereNotNull('PaymentId');
       })->when(request()->get('payment',false) == 'not_paid',function($q){
      $q->whereNull('PaymentId');
      })->latest('CreationDate')->get();

      //  return $orders;


            // Define the Excel spreadsheet headers
        $order_data[] = ['رقم الطلب' ,'العميل','المشكلة','الجهاز' ,'السعر', 'الضريبة', 'العمولة' , 'الفني', 'الحالة' ,'حالة السداد','تاريخ الإنشاء'];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($orders as $k => $order) {

            $order_data[++$k]['Id']         = $order->Id;
            $order_data[$k]['customer']     = $order->customer->full_name;
            $order_data[$k]['issue']        = $order->issue->name;
            $order_data[$k]['device']       = $order->device->name;
            $order_data[$k]['price']        = $order->MaxPrice .' '.t('SAR');
            $order_data[$k]['tax']          = $order->tax.' '.t('SAR');;
            $order_data[$k]['TechnicalCommition']   = $order->TechnicalCommition.' '.t('SAR');;
            $order_data[$k]['technical']    = optional($order->technical)->full_name;

            $order_data[$k]['status']       = $order->status->Name;
            $order_data[$k]['payment']      = $order->payment? 'نعم':'لا';
            $order_data[$k]['CreationDate'] = $order->CreationDate;
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
                $sheet->cells('A1:K1', function($cells) {
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


 public function ExportTechnicalsReport(){

      $search    = $this->request->get('q','');
      $RegionId    = $this->request->get('RegionId',false);
      $model = new User;
      $valid_columns = $model->getFillable();

    $users  =  User::query()->Technical()->Where(function($q)use($valid_columns,$search){
            foreach($valid_columns as $i => $field){
            if($i==0)
            $q->where($field, 'like', "%" . $search . "%");
            else
            $q->OrWhere($field, 'like', "%" . $search . "%");
            }
    })->with('regions')->whereHas('orders',function($q){
    })->when($RegionId,function($q)use($RegionId){
        $q->whereHas('regions',function($q)use($RegionId){
            $q->where('TechnicalRegions.RegionId',$RegionId);
        });

    })->whereHas('transactions',function($q){
      $q->select('UserId')->from('App.Transactions')->where(['OrderStatusId' => 4]);
    })->withCount([ 'orders' => function($q){
       $q->whereIn('Id',function($q){
       $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId' => 4])
       ->whereRaw('[App].[Users].[Id] = UserId');
     });
    }])->get();


//     $Technical_Commition = Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;

      // Define the Excel spreadsheet headers
        $user_data[] = ['م','إسم الفني','رقم الجوال  ','العمولة المسددة ','العمولة  الغير مسددة ','المنطقة'];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($users as $k => $user) {
            // if(!$user->orders_commision_paid and !$user->orders_commision_unpaid)
            //     continue;

            $user_data[++$k]['id'] = $user->Id;
            $user_data[$k]['full_name'] = $user->full_name;
            $user_data[$k]['mobile'] = $user->Mobile;
            $user_data[$k]['orders_commision_paid'] = ($user->orders_commision_paid).' '.t('SAR');;
            $user_data[$k]['orders_commision_unpaid'] = ($user->orders_commision_unpaid).' '.t('SAR');;
            $user_data[$k]['regions'] = $user->regions[0]->Name;

        }
         //  return $user_data;
        // Generate and return the spreadsheet
        \Excel::create('Technicals', function($excel) use ($user_data) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Technicals');
            $excel->setCreator('Laravel')->setCompany('Halenaha');
            $excel->setDescription('Technicals List');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($user_data) {
                $sheet->fromArray($user_data, null, 'A1', false, false);
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


    public function TechnicalsReport()
    {
//        $model = new User;
//        $search    = $this->request->get('q','');
//        $valid_columns = $model->getFillable();
//        $Technical_Commition = \DB::table('App.Orders')->value('TechnicalCommition');
//        dd($Technical_Commition);
//        $userstest  =  User::query()->with('regions','orders')->first()->value('TechnicalCommition')->toArray();
//            ->pluck('TechnicalCommition');
//            ->select('TechnicalCommition');

//            ->Where(function($q)use($valid_columns,$search){
//            foreach($valid_columns as $i => $field){
//                if($i==0)
//                    $q->where($field, 'like', "%" . $search . "%");
//                else
//                    $q->OrWhere($field, 'like', "%" . $search . "%");
//            }
//        });
//        $userstest = User::where(['UserTypeId' => 2])->get();
//        dd($userstest);
     if(request()->ajax()){

//      $search    = $this->request->get('q','');
         $RegionId = $this->request->get('RegionId',false);
//      $RegionId    = $this->request->get('RegionId',false);
//      $model = new User;
//      $valid_columns = $model->getFillable();


//      ->Where(function($q)use($valid_columns,$search){
//            foreach($valid_columns as $i => $field){
//            if($i==0)
//            $q->where($field, 'like', "%" . $search . "%");
//            else
//            $q->OrWhere($field, 'like', "%" . $search . "%");
//            }
//        })

       $users  =  User::whereHas('transactions',function($q){
      $q->select('UserId')->from('App.Transactions')->where(['OrderStatusId' => 4]);
    })
      ->when($RegionId,function($q)use($RegionId){
          $q->whereHas('regions',function($q)use($RegionId){
              $q->where('TechnicalRegions.RegionId',$RegionId);});
      })

      ->withCount([ 'orders' => function($q){
       $q->whereIn('Id',function($q){
       $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId' => 4])
       ->whereRaw('[App].[Users].[Id] = UserId');
     });
    }])->get();

       return datatables()->of($users)->rawColumns(['link_profile'])->make(true);
       }
        $title = t('List of Technicals');
//        $Technical_Commition = Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;
        $regions = Region::get();
        return view('backend.users.index_technicals_report',compact('title','regions'));
    }


 public function ExportTechnicalsMostHaveOrdersReport(){

      $search    = $this->request->get('q','');
      $RegionId    = $this->request->get('RegionId',false);
      $model = new User;
      $valid_columns = $model->getFillable();

       $RegionId = request()->get('RegionId',false);

       // $technicals =  User::Where(function($q)use($valid_columns,$search){
       //      foreach($valid_columns as $i => $field){
       //      if($i==0)
       //      $q->where($field, 'like', "%" . $search . "%");
       //      else
       //      $q->OrWhere($field, 'like', "%" . $search . "%");
       //      }
       //  })->Technical()->has('orders')->with('regions')->withCount('orders')
       // ->when($RegionId,function($q)use($RegionId){
       //  $q->whereHas('regions',function($q)use($RegionId){
       //     $q->where('TechnicalRegions.RegionId',$RegionId);
       //  });})->orderBy('orders_count','desc')->get();


    $technicals  =  User::Where(function($q)use($valid_columns,$search){
            foreach($valid_columns as $i => $field){
            if($i==0)
            $q->where($field, 'like', "%" . $search . "%");
            else
            $q->OrWhere($field, 'like', "%" . $search . "%");
            }
        })->Technical()->whereHas('transactions',function($q){
      $q->select('UserId')->from('App.Transactions')->where(['OrderStatusId' => 4]);
    })->withCount([ 'orders' => function($q){
       $q->whereIn('Id',function($q){
       $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId' => 4])
       ->whereRaw('[App].[Users].[Id] = UserId');
     });
    }])
        ->when($RegionId,function($q)use($RegionId){
        $q->whereHas('regions',function($q)use($RegionId){
           $q->where('TechnicalRegions.RegionId',$RegionId);
        });
    })->with('regions')->orderBy('orders_count','desc')->get();

//dd($technicals);
            // Define the Excel spreadsheet headers
        $user_data[] = ['#','إسم الفني','عدد الطلبات','التقييم '];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($technicals as $k => $user) {

            $user_data[++$k]['id'] = $user->Id;
            $user_data[$k]['full_name'] = $user->full_name;
            $user_data[$k]['orders_count'] = $user->orders_count;
            $user_data[$k]['rating'] = $user->rating_html;
            $user_data[$k]['regions'] = ($user->TechnicalRegions.RegionId);
        }
         //  return $user_data;
        // Generate and return the spreadsheet
        \Excel::create('Technicals', function($excel) use ($user_data) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Technicals');
            $excel->setCreator('Laravel')->setCompany('Halenaha');
            $excel->setDescription('Technicals List');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($user_data) {
                $sheet->fromArray($user_data, null, 'A1', false, false);
                $sheet->setRightToLeft(true);
                $sheet->cells('A1:D1', function($cells) {
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


    public function TechnicalsMostHaveOrdersReport()
    {

     if(request()->ajax()){

       $RegionId = request()->get('RegionId',false);


    $technicals  =  User::whereHas('transactions',function($q){
      $q->select('UserId')->from('App.Transactions')->where(['OrderStatusId' => 4]);
    })->withCount([ 'orders' => function($q){
       $q->whereIn('Id',function($q){
       $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId' => 4])
       ->whereRaw('[App].[Users].[Id] = UserId');
     });
    }])
        ->when($RegionId,function($q)use($RegionId){
        $q->whereHas('regions',function($q)use($RegionId){
           $q->where('TechnicalRegions.RegionId',$RegionId);
        });
    })->with('regions')->get();

       return datatables()->of($technicals)->rawColumns(['link_profile'])
       ->make(true);

       }

        $title = t('List of Technicals Have Most Orders');
        $regions = Region::get();
        return view('backend.users.index_technicals_most_have_orders_report',compact('title','regions'));
    }














}
