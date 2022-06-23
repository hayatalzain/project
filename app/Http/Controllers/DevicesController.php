<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceIssue;
use App\Models\Issue;
use App\Models\Brand;
use DataTables;
class DevicesController extends Controller
{

    public function index()
    {
//        $model = Device::query()->with('brands')->orderBy('OrderNo')->get();
//        dd($model);
        if(request()->ajax()){
            $BrandId = request()->get('BrandId',false);
            $model = Device::query()->with('brands')
                ->when($BrandId,function($q)use($BrandId){
                $q->where('BrandId',$BrandId);
            }
            )->orderBy('OrderNo')->get() ;
//                ->toArray()


            return DataTables()
                ->of($model)
                ->make(true);
       }
        $brands = Brand::get();
        $title = t('List of Devices');
        return view('backend.devices.index',compact('title','brands'));
    }

//    public function ExportAllDevices(){
////        $search    = $this->request->get('q','');
////        $model = new DeviceIssue;
////        $valid_columns = $model->getFillable();
////        $BrandId = request()->get('brands.NameAr',false);
////        $issues = DeviceIssue::with('issue')->where('DeviceId',$id)->get();
//
//        $devices = Device::query()->with('issues')
////            ->when($BrandId,function($q)use($BrandId){
////                $q->where('BrandId',$BrandId);
////            })
//
////            ->Where(function($q)use($valid_columns,$search){
////            foreach($valid_columns as $i => $field){
////                if($i==0)
////                    $q->where($field, 'like', "%" . $search . "%");
////                else
////                    $q->OrWhere($field, 'like', "%" . $search . "%");
////            }
////        })
//            ->latest('CreationDate')->get()
//
//            ->toArray();
//
////        dd($devices);
//        // Define the Excel spreadsheet headers
//        $device_data[] = ['اسم الجهاز','إسم المشكلة', 'السعر' , 'تاريخ التسجيل'];
//        // Convert each member of the returned collection into an array,
//        // and append it to the phones array.
//            foreach ($devices as $k => $device) {
////            dd($device);
////            $device_data[++$k]['id'] = $device->id;
////            $device_data[$k]['issues']     = $device->issues->name;
////            $device_data[$k]['issues']     = $device->MaxPrice;
////            $device_data[$k]['issues']     = $device->pivot->MaxPrice;
////            $device_data[$k]['issues']     = $device->issues->pivot_MaxPrice;
//            $device_data[$k]['Id'] = $device->Id;
//
//        }
//        //  return $issue_data;
//        // Generate and return the spreadsheet
//        \Excel::create('devices', function($excel) use ($device_data) {
//
//            // Set the spreadsheet title, creator, and description
//            $excel->setTitle('devices');
//            $excel->setCreator('Laravel')->setCompany('Halenaha');
//            $excel->setDescription('devices List');
//
//            // Build the spreadsheet, passing in the payments array
//            $excel->sheet('sheet1', function($sheet) use ($device_data) {
//                $sheet->fromArray($device_data, null, 'A1', false, false);
//                $sheet->setRightToLeft(true);
//                $sheet->cells('A1:G1', function($cells) {
//                    /*   $cells->setFontSize(15);
//                       $cells->setFontWeight('bold'); */
//                    $cells->setFont([
//                        'name' => 'arial',
//                        'size' => 14,
//                        'bold' => true
//                    ]);
//                    $cells->setBackground('#AAAAFF');
//                    $cells->setFontColor("#4d4d4d");
//                    $cells->setAlignment('center');
//                    $cells->setValignment('center');
//
//                });
//            });
//
//        })->download('xlsx');
//
//
//    }


    public function edit($id)
    {
        $device =  Device::findOrFail($id);
        $brands=Brand::pluck('NameAr', 'Id')->toArray();

        $title = t('Edit Device',['username'=>$device->NameAr]);
        return view('backend.devices.form',compact('device','title','brands'));
    }

    public function update($id)
    {
           $rules = [
            'NameAr'         => 'required|max:191',
            'NameEn'         => 'required',
            'BrandId'         => 'required',
            'DeviceStatusId' => 'required',
               'OrderNo'         => 'max:255|integer',

           ];

        $data =  $this->request->validate($rules);
        $device =  Device::findOrFail($id);
        $device->update($data);
        return redirect()->route('devices')->with('success',t('successfully edited'));

    }

   public function create()
    {
         $title = t('Add New Device');
         $brands=Brand::pluck('NameAr', 'Id')->toArray();
        return view('backend.devices.form',compact('title','brands'));
    }

    public function store()
    {
           $rules = [
            'NameAr'         => 'required|max:191',
            'NameEn'         => 'required',
            'BrandId'         => 'required',
            'DeviceStatusId' => 'required|integer',
            'OrderNo'         => 'required|max:255|integer',
           ];

        $data =  $this->request->validate($rules);
        $test=Device::where('OrderNo','=',request()->input('OrderNo'))->first();
        if($test){
            return redirect()->route('devices')->with('error',t('القيمة محجوزة '));
        }else {
            $data['CreatedBy'] = 'admin';
            $deviceD = Device::create($data);
            return redirect()->route('devices')->with('success',t('successfully added'));

        }

    }
    public function show(){

    }
    public function destroy($id)
    {
        $device = Device::with(['device_issues_Id'])->find($id);
        $device->device_issues_Id()
        ->update(['DeviceId' => null]);
        $device->delete();
           return redirect()->route('devices')->with('success',t('successfully deleted'));
    }

    public function issuesPrice($id)
    {
        $issues = DeviceIssue::with('issue')->where('DeviceId',$id)->get();
        $title = t('List of Issues');
        return view('backend.devices.issues.index',compact('title','issues','id'));
    }

    public function editIssuePrice($id,$issue)
    {
        $device =  DeviceIssue::with('issue')->findOrFail($issue);
        $title = t('Edit Issue Price',['username'=>$device->issue->name]);
        return view('backend.devices.issues.form',compact('device','title'));
    }

    public function updateIssuePrice($id,$issue)
    {
           $rules = [
          //  'MinPrice'       => 'required|numeric',
            'MaxPrice'       => 'required|numeric',
            'Description'    => 'sometimes',
            'StatusId'       => 'required|boolean',
        ];

        $data =  $this->request->validate($rules);
        $device =  DeviceIssue::where('Id',$issue)->firstOrFail();
        $device->update($data);
        return redirect()->route('devices.issues-price',$id)->with('success',t('successfully edited'));

    }

    public function createIssuePrice($id)
    {
         $title = t('Add New Issue Price');
         $DeviceId = $id;
         $fieldName = isRtl()?'NameAr':'NameEn';
         $issues =  Issue::whereDoesntHave('devices', function ($query)use($id){
           $query->where('Device.Id',$id);
         })->pluck($fieldName,'Id');
        return view('backend.devices.issues.form',compact('title','DeviceId','issues'));
    }

    public function storeIssuePrice($id)
    {
           $rules = [
            'IssueId'        => 'required|numeric',
         //   'MinPrice'       => 'required|numeric',
            'MaxPrice'       => 'required|numeric',
            'Description'    => 'sometimes',
            'StatusId'       => 'required|boolean',
        ];

        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = 'admin';
        $data['DeviceId']  = $id;
        $deviceD = DeviceIssue::create($data);

        return redirect()->route('devices.issues-price',$id)->with('success',t('successfully added'));

    }

  public function DestroyIssuesPrice($id,$issue)
    {
           $device =  DeviceIssue::where('Id',$issue)->delete();
           return back()->with('success',t('successfully deleted'));
    }
    public function ExportAllDevices(){
//        $search    = $this->request->get('q','');
//        $model = new DeviceIssue;
//        $valid_columns = $model->getFillable();
        $BrandId = request()->get('BrandId',false);

        $devices = DeviceIssue::with('issue','device')
            ->when($BrandId,function($q)use($BrandId){
                $q->where('BrandId',$BrandId);
            })

//            ->Where(function($q)use($valid_columns,$search){
//            foreach($valid_columns as $i => $field){
//                if($i==0)
//                    $q->where($field, 'like', "%" . $search . "%");
//                else
//                    $q->OrWhere($field, 'like', "%" . $search . "%");
//            }
//        })
            ->latest('CreationDate')
           ->get();

//        dd($devices);
        // Define the Excel spreadsheet headers
        $device_data[] = ['اسم الجهاز','إسم المشكلة', 'السعر' , 'تاريخ التسجيل'];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($devices as $k => $device) {

            $device_data[++$k]['device'] = $device->device->NameAr;
            $device_data[$k]['issue']     = $device->issue->NameAr;
            $device_data[$k]['issues']     = t($device->MaxPrice);
            $device_data[$k]['CreationDate'] = $device->CreationDate;

        }
        //  return $issue_data;
        // Generate and return the spreadsheet
        \Excel::create('devices', function($excel) use ($device_data) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('devices');
            $excel->setCreator('Laravel')->setCompany('Halenaha');
            $excel->setDescription('devices List');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($device_data) {
                $sheet->fromArray($device_data, null, 'A1', false, false);
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
