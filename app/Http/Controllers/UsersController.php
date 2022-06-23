<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Auth;
use App\Rules\CheckPassword;
use App\Models\User;
use App\Models\Region;
use App\Models\Setting;
use App\Models\UserDevicesFavorites;
use App\Models\Device;
use App\Models\Issue;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Transaction;
use DB;
use Illuminate\Validation\Rule;


class UsersController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

// start Controller to Favorite Devices and Issue

    public function FavoriteDevices($id)
    {
        $title = t('Favorite devices for a technician');
        $devices = UserDevicesFavorites::with('issueFavorite','deviceFavorite')->where('UserId',$id)->get();
        $devicesList=Device::where('DeviceStatusId','=','2')->get()->All();
        $issuesList=Issue::where('IssueStatusId','=','2')->get();
        return view('backend.users.index_devices',compact('title','devices','id','issuesList','devicesList'));
    }

    public function EditFavoriteDevices($id,$issue)
    {
        $device =  UserDevicesFavorites::with('issueFavorite','deviceFavorite')->findOrFail($issue);
        $issuesListEdit=Issue::where('IssueStatusId','=','2')->get();
        $devicesListEdit=Device::where('DeviceStatusId','=','2')->get();
        $title = t('Edit Favorite Device ',['username'=>$device->issueFavorite->name]);
        return view('backend.users.form_devices',compact('device','title','issue','issuesListEdit','devicesListEdit'));
    }
    public function UpdateFavoriteDevices($id,$issue)
    {
        $rules = [
            'DeviceId'       => 'required',
            'IssueId'       => 'required',
        ];

        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = 'admin';
        $data['UserId']  = $id;
        $deviceD = UserDevicesFavorites::where('UserId' ,'=',$id)
            ->where('DeviceId' ,'='  ,$data['DeviceId'])
            ->where('IssueId' ,'='  ,$data['IssueId'])->first();

        if (empty($deviceD)){
            $device=UserDevicesFavorites::where('Id',$issue)->firstOrFail();
            $device->update($data);
            return redirect()->route('favorite_devices',$id)->with('success',t('successfully edited'));
        }else {
            return redirect()->route('favorite_devices',$id)->with('error',t('العناصر المدخلة الموجودة مسبقاً!'));

        }
    }

    public function StoreFavoriteDevices($id)
    {
        $rules = [
            'DeviceId'       => 'required',
            'IssueId'       => 'required',
        ];

        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = 'admin';
        $data['UserId']  = $id;

        $title = t('Add New Favorite Devices');
        $deviceD = UserDevicesFavorites::where('UserId' ,'=',$id)
            ->where('DeviceId' ,'='  ,$data['DeviceId'])
            ->where('IssueId' ,'='  ,$data['IssueId'])->first();

        if (empty($deviceD)){
            $deviceD =UserDevicesFavorites::create($data);

            return redirect()->route('store_favorite_devices',$id)->with('success',t('تمت عملية الاضافة بنجاح'));
        }else {
            return redirect()->route('store_favorite_devices',$id)->with('error',t('   العناصر المدخلة الموجودة مسبقاً !'));
        }


    }
    public function AllFavoriteDevices1($id)
    {
        $title = t('Favorite devices for a technician');

        $data=DB::update('EXEC CreateFavoritesDevices ?,?',array( $id ,1) );
//dd($data);
//        DB::select(DB::raw("exec CreateFavoritesDevices :UserId, :result"),[
//            ':UserId' => $id,
//            ':result' => $result,
//        ]);
        return redirect()->route('store_favorite_devices',$id)->with('success',t('تم اضافة المفضلة بنجاح'));;
    }

    public function CraeteFavoriteDevices($id)
    {
        $DeviceId = $id;
        $device =  UserDevicesFavorites::with('issueFavorite','deviceFavorite')->findOrFail($id);

        return view('backend.users.index_devices',compact('DeviceId','device'));
    }

    public function DestroyFavoriteDevices($id,$issue)
    {
        $device =  UserDevicesFavorites::where('Id',$issue)->delete();
        return back()->with('success',t('successfully deleted'));
    }

    public function DestroyFavoriteDevicesAll($id)
    {
        $device =   UserDevicesFavorites::where('UserId','=',$id)->delete();

        return back()->with('success',t('successfully deleted'));
    }
// end Controller to Favorite Devices and Issue

    public function clients()
    {
        if(request()->ajax()){

            $IsConnected = request()->get('IsConnected',null);
            $RegionId = request()->get('RegionId',false);
            $UserStatusId = request()->get('UserStatusId','all');
            return datatables()->of(User::query()
                ->when($UserStatusId != 'all',function($q)use($UserStatusId){
                $q->where('UserStatusId',$UserStatusId);
            })->when(!is_null($IsConnected),function($q)use($IsConnected){
                $q->where('IsConnected',$IsConnected);
            })->when($RegionId,function($q)use($RegionId){
                $q->whereHas('regions',function($q)use($RegionId){
                    $q->where('TechnicalRegions.RegionId',$RegionId);
                });
            })->where(['UserTypeId' => 1]))
                ->rawColumns(['link_profile'])
                ->make(true);
        }
        $title = t('List of Clients');
        $regions = Region::get();
        return view('backend.users.index_clients',compact('title','regions'));
    }
    // test
    public function technicalsTest()
    {
        $user =User::with('transactions')->where('UserTypeId',2)->first();
//            ->where('Id','=',16)->get();
//            ->where('Id','=','TechnicalId')
//            ->groupBy('TechnicalId')->avg('Value');
//       ->groupBy('TechnicalId')->avg('Value');
//        $user =User::where('UserTypeId', 2)->
//        withCount('transactions',function ($query){
//            $query->where('OrderStatusId',4);
//        })->fisrt();
//          ->where(['UserTypeId' => 2])
        dd($user['transactions']);
        if(request()->ajax()){
            $IsConnected = request()->get('IsConnected',null);
//            $RegionId = request()->get('RegionId',false);
            $UserStatusId = request()->get('UserStatusId','all');
//            $user = User::where(['UserTypeId' => 2])

            $user =User::where(['UserTypeId' => 2])
                ->withCount("transactions")->get()
                ->when($UserStatusId != 'all',function($q)use($UserStatusId){
                    $q->where('UserStatusId',$UserStatusId);
                })
                ->when(!is_null($IsConnected),function($q)use($IsConnected){
                    $q->where('IsConnected',$IsConnected);
                });
//                ->when(!is_null($rating_html),function($q)use($rating_html){
//                    $q->where('rating_html',$rating_html);
//                })
//                ->when($RegionId,function($q)use($RegionId){
//                    $q->whereHas('regions',function($q)use($RegionId){
//                        $q->where('TechnicalRegions.RegionId',$RegionId);});
//                });
//                ->get();
//                ->Technical()->get();


            return datatables()->of($user)
                ->rawColumns(['link_profile'])
                ->toJson();
//                ->make(true);

//            $data['technicians_most_orders']  =  User::Technical()->whereHas('transactions',function($q){
//                $q->select('UserId')->from('App.Transactions')->where(['OrderStatusId' => 4]);
//            })->withCount([ 'orders' => function($q){
//                $q->whereIn('Id',function($q){
//                    $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId' => 4])
//                        ->whereRaw('[App].[Users].[Id] = UserId');
//                });
//            }]);
//                ->orderBy('orders_count','desc')->take(9)->get();

        }
        $title = t('List of Technicals');
        $regions = Region::get();
        $Technical_Commition = Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;
        return view('backend.users.index_technicals_test',compact('title','regions','Technical_Commition'));
    }
// main fun
    public function technicals()
    {
//        $user =User::first();
//      return  $user->makeHidden('FirstName');
//////            ->pluck('region_name', 'total_invoices', 'commission_invoices')
//////            ->pluck('total_invoices')
//////            ->get();
//////                           ->getBlock('region_name')
//////                        ->select(['regions.region_name','count_invoices','total_invoices','commission_invoices'])
////     ;
//        dd($user);
        if(request()->ajax()){
            $IsConnected = request()->get('IsConnected',null);
            $RegionId = request()->get('RegionId',false);
            $UserStatusId = request()->get('UserStatusId','all');

            $user = User::where(['UserTypeId' => 2])
//                ->whereHas('transactions',function($q) {
//                    $q->select('UserId')->from('App.Transactions')->where(['UserTypeId' => 2]);
//                })
                ->when($RegionId,function($q)use($RegionId){
                    $q->whereHas('regions',function($q)use($RegionId){
                        $q->where('TechnicalRegions.RegionId',$RegionId);
                    });
                })
                ->when($UserStatusId != 'all',function($q)use($UserStatusId){
                    $q->where('UserStatusId',$UserStatusId);
                })
                ->when(!is_null($IsConnected),function($q)use($IsConnected){
                    $q->where('IsConnected',$IsConnected);
                })
                ->get();
//                ->when($UserStatusId != 'all',function($q)use($UserStatusId){
//                    $q->where('UserStatusId',$UserStatusId);
//                })
//                ->when(!is_null($IsConnected),function($q)use($IsConnected){
//                    $q->where('IsConnected',$IsConnected);
//                })
//                ->when(!is_null($rating_html),function($q)use($rating_html){
//                    $q->where('rating_html',$rating_html);
//                })
//                ->when($RegionId,function($q)use($RegionId){
//                    $q->whereHas('regions',function($q)use($RegionId){
//                        $q->where('TechnicalRegions.RegionId',$RegionId);});
//                })
//                ->with('regions','orders');
//            ->get();

            return DataTables()::of($user)
                ->rawColumns(['link_profile'])
                ->make(true);
//                ->orderColumn('rating_html',true)
//                ->toJson();

//                /->select('link_profile')
//                ->getBlock('region_name')
//                ->toArray();
//                ->orderColumn('rating_html')


//            $data['technicians_most_orders']  =  User::Technical()->whereHas('transactions',function($q){
//                $q->select('UserId')->from('App.Transactions')->where(['OrderStatusId' => 4]);
//            })->withCount([ 'orders' => function($q){
//                $q->whereIn('Id',function($q){
//                    $q->select('OrderId')->from('App.Transactions')->where(['OrderStatusId' => 4])
//                        ->whereRaw('[App].[Users].[Id] = UserId');
//                });
//            }])->orderBy('orders_count','desc')->take(9)->get();

        }
        $title = t('List of Technicals');
        $regions = Region::get();
//        $Technical_Commition = Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;
        return view('backend.users.index_technicals',compact('title','regions'));
    }

    public function edit($id)
    {
        $user = User::with('regions')->findOrFail($id);
        $regions = Region::where('IsActive',true)->pluck('Name','Id');
        $user->RegionId = 0;

        if(isset($user->regions[0]))
            $user->RegionId = $user->regions[0]->Id;

            $title = t('Edit Account',['username'=>$user->full_name]);
        $order =  Order::find($id);
//        dd($order);
        return view('backend.users.form',compact('user','title','regions','order'));
    }

    public function update($id)
    {
        $user =  User::findOrFail($id);
        $rules = [
            'FirstName'              => 'required|max:191',
            'LastName'               => 'sometimes|max:191',
            'Email'                  => 'required|email|unique:sqlsrv.App.Users,Email,'.$user->Id,
            'IdentityNo'             => 'sometimes', //unique:sqlsrv.App.Users,IdentityNo,'.$user->Id,
            //  'Password'               => 'sometimes|nullable|min:6',
            'Mobile'                 => 'required',
            'RegionId'               => 'sometimes',
            'UserStatusId'           => 'required|integer',
            'AllowNotifications'     => 'required|boolean',
            'IsSpecial'     => 'boolean',
        ];

        $data =  $this->request->validate($rules);
        $user->update($data);


        if(request()->has('RegionId')){
            $user->regions()->sync($data['RegionId']);
        }
        $route = 'technicals';
        if($user->UserTypeId == 1)
            $route = 'clients';

        return redirect()->route($route)->with('success',t('Successfully Updated'));

    }

    public function MyAccount()
    {
        return view('backend.my-profile');
    }

    public function UpdateMyAccount()
    {
        $user =  Auth::user();
        $rules = [
            'full_name'    => 'required|max:191',
            'username'     => 'required|unique:users,username,'.$user->id,
            'email'        => 'required|email|unique:users,email,'.$user->id,
            'password'     => 'sometimes|nullable|min:6|confirmed',
            'old_password' => ['required_with:password','nullable','min:6',new CheckPassword($user->password)],
            //   'mobile'     => 'required|unique:users,id,'.$user->id,
        ];
        //   dd($this->request->all());
        $data =  $this->request->validate($rules);
        $user->update($data);
        return back()->with('success',t('Successfully Updated'));
    }

    public function ExportAllTechnicals(){
//        $IsConnected = request()->get('IsConnected',null);
        $RegionId = request()->get('RegionId',false);
        $UserStatusId = request()->get('UserStatusId','all');

        $users =  User::where(['UserTypeId' => 2])->with('regions')->withCount('orders')
            ->when($UserStatusId != 'all',function($q)use($UserStatusId){
            $q->where('UserStatusId',$UserStatusId);
        })

            ->when($RegionId,function($q)use($RegionId){
                $q->whereHas('regions',function($q)use($RegionId){
                    $q->where('TechnicalRegions.RegionId',$RegionId);});
            })

            ->Technical()->latest('CreationDate')->get();

//        $Technical_Commition = Setting::where('SettingKey','Technical_Commition')->first()->SettingValue;

        // Define the Excel spreadsheet headers
        $user_data[] = ['م','إسم الفني','الايميل','الجوال','عدد الطلبات','إجمالي الفواتير' ,'الضريبة','العمولة ','المنطقة','الحالة'];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($users as $k => $user) {
            // if(!$user->orders_commision_paid and !$user->orders_commision_unpaid)
            //     continue;

            $user_data[++$k]['id'] = $user->Id;
            $user_data[$k]['full_name'] = $user->full_name;
            $user_data[$k]['Email'] = $user->Email;
            $user_data[$k]['Mobile'] = $user->Mobile;
            $user_data[$k]['orders_count'] =  $user->orders_count;
            $user_data[$k]['total_invoices'] = $user->total_invoices;
            $user_data[$k]['tax'] =  $user->tax_invoices;
//         $user_data[$k]['OrdersCommision'] = ($user->orders_commisionpaid+$user->orders_commision_usnpaid)*$Technical_Commition;
            $user_data[$k]['commission_invoices'] = $user->commission_invoices;
            $user_data[$k]['region_name'] = $user->region_name;
            $user_data[$k]['UserStatusId'] = t($user->user_statuses->Name);

//            if($user->UserStatusId==1)
//                $user_data[$k]['status'] = 'غير فعال ';
//            if($user->UserStatusId==2)
//                $user_data[$k]['status'] = 'فعال';
//            if($user->UserStatusId==3)
//                $user_data[$k]['status'] = 'محظور';
        }
//        return $user_data;
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

    public function ExportAllClients(){
        $IsConnected = request()->get('IsConnected',null);
        $RegionId = request()->get('RegionId',false);
        $UserStatusId = request()->get('UserStatusId','all');

                $users =  User::when($UserStatusId != 'all',function($q)use($UserStatusId){
                    $q->where('UserStatusId',$UserStatusId);
                })->when(!is_null($IsConnected),function($q)use($IsConnected){
                    $q->where('IsConnected',$IsConnected);
                })->when($RegionId,function($q)use($RegionId){
                    $q->whereHas('regions',function($q)use($RegionId){
                        $q->where('TechnicalRegions.RegionId',$RegionId);
                    });
                })->where(['UserTypeId' => 1])->get();


                // Define the Excel spreadsheet headers
                $user_data[] = ['م','إسم العميل ','الايميل','الجوال'];

                foreach ($users as $k => $user) {
                    // if(!$user->orders_commision_paid and !$user->orders_commision_unpaid)
                    //     continue;

                    $user_data[++$k]['id'] = $user->Id;
                    $user_data[$k]['full_name'] = $user->full_name;
                    $user_data[$k]['Email'] = $user->Email;
                    $user_data[$k]['Mobile'] = $user->Mobile;
                }
        //        return $user_data;
                // Generate and return the spreadsheet
                \Excel::create('Clients', function($excel) use ($user_data) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Clients');
                    $excel->setCreator('Laravel')->setCompany('Halenaha');
                    $excel->setDescription('Clients List');

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


}
