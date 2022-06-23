<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\RegionDetail;
use Auth;

class RegionsDetailsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = t('Neighborhoods List');
        return view('backend.regions.details.index',compact('title'));
    }

    public function edit($id)
    {

        $neighborhood =  RegionDetail::findOrFail($id);
        $title = t('Edit Neighborhood');
        $region = null;
        $regions = Region::pluck('Name','Id');    

        return view('backend.regions.details.form',compact('neighborhood','title','region','regions'));
    }




    public function update($id)
    {


        $neighborhood =  RegionDetail::findOrFail($id);

           $rules = [
            'Name' => 'required|max:191',
            'RegionId' => 'required|max:191',
            'Lat' => 'required',
            'Long' => 'required',
            'Radius' => 'required',
            'IsActive' => 'required|boolean',
        ];

        $data  =  $this->request->validate($rules);
        $data['UpdatedBy'] = Auth::user()->username;
        $neighborhood->update($data);
        return redirect()->route('regions_details',['region_id'=>$neighborhood->RegionId])->with('success',t('successfully edited'));

    }



   public function create()
    {
         $title = t('Add New Neighborhood');
         $region = null;
         $regions = null;
         if(request()->has('region_id'))
         $region = Region::findOrFail(request()->get('region_id'));
         else
         $regions = Region::pluck('Name','Id');            
        return view('backend.regions.details.form',compact('title','region','regions'));
    }


    public function store()
    {

           $rules = [
            'Name' => 'required|max:191',
            'RegionId' => 'required|max:191',
            'Lat' => 'required',
            'Long' => 'required',
            'Radius' => 'required',
            'IsActive' => 'required|boolean',
        ];

        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = Auth::user()->username;
        $neighborhood = RegionDetail::create($data);

        return redirect()->route('regions_details',['region_id'=>$neighborhood->RegionId])->with('success',t('successfully added'));


    }



    public function destroy($id)
    {
           $neighborhood =  RegionDetail::find($id);
           $RegionId = $neighborhood->RegionId;
           $neighborhood ->delete();
           return redirect()->route('regions_details',['region_id'=>$RegionId])->with('success',t('successfully edited'));
    }





}
