<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RejectReason;
use Auth;

class RejectReasonsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       if(request()->ajax()){
       return datatables()->of(RejectReason::query()->with('user_type'))->make(true);
       }

        $title = t('Reasons List ');
        return view('backend.reject_reasons.index',compact('title'));
    }

    public function edit($id)
    {

        $reason = RejectReason::findOrFail($id);
        $title = t('Edit Reason');
        return view('backend.reject_reasons.form',compact('reason','title'));
    }


    public function update($id)
    {
           $rules = [
            'Name'       => 'required|max:50',
            'UserTypeId' => 'required',
        ];

        $data  =  $this->request->validate($rules);
        $reason = RejectReason::findOrFail($id);
        $reason->update($data);
        return redirect()->route('reject_reason')->with('success',t('successfully edited'));

    }



   public function create()
    {
         $title = t('Add New Reason');
        return view('backend.reject_reasons.form',compact('title'));
    }


    public function store()
    {

           $rules = [
            'Name'       => 'required|max:50',
            'UserTypeId' => 'required',
        ];


        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = Auth::user()->username;
        $reason =   RejectReason::create($data);

        return redirect()->route('reject_reason')->with('success',t('successfully added'));


    }



    public function destroy($id)
    {
           $reason = RejectReason::destroy($id);
           return redirect()->route('reject_reason')->with('success',t('successfully edited'));
    }





}
