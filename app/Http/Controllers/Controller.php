<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(Request $request)
    {

        $this->request = $request;

        $agent = new \stdClass();
        if(!is_null($this->request->route()))
        $agent->current_route = $this->request->route()->getName();

        view()->share('agent', $agent);

       // $this->request['Auth-Role'] = $this->getRole();
    }


    protected function getRole()
    {
        $role = $this->request->header('Auth-Role');
        return $role;
    }


    public function dataTableData(Request $request)
    {

        $model = 'App\Models\\' . $request->model;
        $model = new $model;
       // return get_class($model);
        $valid_columns = $model->getFillable();

        $columns = $request->get('columns');
        $response['draw'] = intval($request->get('draw'));
        $start = intval($request->get('start'));
        $length = intval($request->get('length'));
        $order_column_id = $request->get('order')[0]['column'];
        $order = $columns[$order_column_id]['data'];
        $b_order = $order;
        $order = in_array($order, $valid_columns) ? $order : 'id';
        $dir = $request->get('order')[0]['dir'];
        $dir = in_array($dir, ['asc', 'desc']) ? $dir : 'desc';
        $search = $request->get('search')['value'];
        $route = $request->get('route',false);

       //  return ($request->get('filters'));
        // return $request->all();
     // return  request()->get('payment',false);
         $filters =[];
         $filters_1 =[];
         if($request->has('filters') and !is_array($request->get('filters')))
         unset($request['filters']);


         if($request->has('filters') and !empty($request->get('filters'))){

            $filters = array_except(array_filter(($request->get('filters')),function($v){return !is_null($v);}),['date','start_date','end_date','api_token','state']);
            $filters_1 = array_only(array_filter(($request->get('filters')),function($value){return !is_null($value);}),['date','start_date','end_date']);

            foreach ($valid_columns as $key => $field) {
                if(array_key_exists($field,$filters))
                 unset($valid_columns[$key]);
               }

         }
        // dd($request->get('filters'));
        if(array_key_exists('platform',$filters) and $filters['platform']=='all')
             unset($filters['platform']);

        $count_all = $model::count();

        $query = $model::dataTable()->Where(function($q)use($valid_columns,$search){
            foreach($valid_columns as $i => $field){
            if($i==0)
            $q->where($field, 'like', "%" . $search . "%");
            else
            $q->OrWhere($field, 'like', "%" . $search . "%");
            }
        })->where(function($q)use($request,$filters,$filters_1){
             $q->when($request->has('filters'),function($q)use($filters){
             foreach($filters as $field => $value){
             $q->where($field,$value);
             }
             });
             $q->when(request()->get('payment',false) == 'paid' , function($q){
                $q->whereNotNull('PaymentId');
             })->when(request()->get('payment',false) == 'not_paid',function($q){
                $q->whereNull('PaymentId');
             })->when(array_key_exists('date',$filters_1),function($q)use($filters_1){
             $q->whereDate('CreationDate',$filters_1['date']);
            })->when(array_key_exists('start_date',$filters_1),function($q)use($filters_1){
             $q->whereDate('CreationDate','>=',$filters_1['start_date']);
            })->when(array_key_exists('end_date',$filters_1),function($q)use($filters_1){
             $q->whereDate('CreationDate','<=',$filters_1['end_date']);
            });
        });


        $response['count_filtered'] = $query->count();


         if($route == 'reports.financial') {

         $response['total_invoices'] = round($query->sum('MaxPrice'));

        // $response['total_invoices'] = round(\DB::table('App.Orders')->when(request()->get('payment',false) == 'paid' , function($q){
        //         $q->whereNotNull('PaymentId');
        //      })->when(request()->get('payment',false) == 'not_paid' , function($q){
        //         $q->whereNull('PaymentId');
        //      })->sum('MaxPrice'));

        $response['total_tax'] = round(($response['total_invoices'] * 5 ) / 100);

             $response['total_commission']  = \DB::table('App.Orders')
                 ->whereIn('Id',function($q){
                     $q->select('OrderId')->from('App.Transactions')->where(['TechnicalId'=> $this->Id,'OrderStatusId' => 4]);
                 })->sum('TechnicalCommition');

//        $response['total_commission']  = round(\App\Models\Setting::where('SettingKey','Technical_Commition')->first()->SettingValue *  $response['count_filtered']);

         }


        $response['data'] = $query->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)->get()->toArray();

        /*

->filter(function($item)use($model){
          //  if(get_class($model)=='App\Models\Market'){
            if(!$item->online)
             return $item;
            // }
            // return $item;
        })

        $count_filtered = count($data);
   */
        $response["recordsTotal"] = $count_all;


        return response($response);
    }














}
