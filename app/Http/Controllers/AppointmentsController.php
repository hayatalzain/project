<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Days;
use App\Models\Appointments;
use DataTables;
use phpDocumentor\Reflection\Types\Null_;

class AppointmentsController extends Controller
{
    public function index()
    {
        if(request()->ajax()){

            $model = Appointments::with('days')
                ->where('IsDeleted','=','0');
            return DataTables::eloquent($model)
                ->make(true);

        }

        $title = t('List of appointments');
        return view('backend.appointments.index',compact('title'));
    }

    public function edit($id)
    {
        $appointments =  appointments::findOrFail($id);

        $days=Days::pluck('NameAr', 'Id')->toArray();
        $title = t('Edit appointments',['username'=>$appointments->NameAr]);
        return view('backend.appointments.form',compact('appointments','title','days'));
    }

    public function update($id)
    {
           $rules = [
            'Name'         => 'required|max:191',
            'DayId'         => 'required',
               'StartTime' => 'required|before:EndTime|date_format:H:i',
               'EndTime' =>'required|date_format:H:i',
        ];


        $data =  $this->request->validate($rules);

        $test=appointments::where('Id','!=',$id)
        ->where('DayId','=',$data['DayId'])
            ->where('StartTime','>=',$data['StartTime'])
            ->where('EndTime','<=', $data['EndTime'])
            ->where('IsDeleted','=',false)
            ->first();
        if($test == NULL){
            $appointments =  appointments::findOrFail($id);
            $appointments->update($data);
        }else{
            return redirect()->route('appointments.edit',$id)->with('error',t('الفترة مدخلة موجودة'))  ;

        }
        return redirect()->route('appointments')->with('success',t('successfully edited'));

    }

   public function create()
    {
         $title = t('Add New appointments');
        $days=Days::pluck('NameAr', 'Id')->toArray();
        return view('backend.appointments.form',compact('title','days'));
    }

    public function store()
    {
        $rules = [
               'Name'         => 'required|max:191',
               'DayId'         => 'required',
               'StartTime' => 'required|before:EndTime|date_format:H:i',
               'EndTime' =>'required|date_format:H:i',
        ];
        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = 'admin';
        $data['IsDeleted'] = false;

        $test=appointments::where('DayId','=',$data['DayId'])
            ->where('StartTime','>=',$data['StartTime'])
            ->where('EndTime','<=', $data['EndTime'])
            ->where('IsDeleted','=',false)
            ->first();

        if($test == NULL) {
            $appointments = appointments::create($data);
        }

        else{
            return redirect()->route('appointments.create')->with('error',t('الفترة مدخلة موجودة'))  ;
        }


        return redirect()->route('appointments')->with('success',t('successfully edited'))  ;
    }

    public  function show(){

    }
    public function destroy($id)
    {
        $appointments = Appointments::find($id);
        $appointments->update(['IsDeleted' => 1]);
        return redirect()->route('appointments')->with('success',t('successfully deleted'));
    }


}
