<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use Auth;

class RegionsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       if(request()->ajax()){
       return datatables()->of(Region::query()->withCount('regions_details'))->make(true);
       }

        $title = t('Regions List');
        return view('backend.regions.index',compact('title'));
    }

    public function edit($id)
    {

        $region =  Region::findOrFail($id);
        $title = t('Edit Region');
        return view('backend.regions.form',compact('region','title'));
    }




    public function update($id)
    {


        $region =  Region::findOrFail($id);

           $rules = [
            'Name' => 'required|max:191',
            'IsActive' => 'required|boolean',
        ];

        $data  =  $this->request->validate($rules);
        $data['UpdatedBy'] = Auth::user()->username;
        $region->update($data);
        return redirect()->route('regions')->with('success',t('successfully edited'));

    }



   public function create()
    {
         $title = t('Add New Region');
        return view('backend.regions.form',compact('title'));
    }


    public function store()
    {

           $rules = [
            'Name' => 'required|max:191',
            'IsActive' => 'required|boolean',
        ];

        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = Auth::user()->username;
        $region = Region::create($data);

        return redirect()->route('regions')->with('success',t('successfully added'));


    }



    public function destroy($id)
    {
           $region =  Region::destroy($id);
           return redirect()->route('regions')->with('success',t('successfully edited'));
    }





}
