<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ColorType;


class ColorsController extends Controller
{

    public function index()
    {

        if(request()->ajax()){
            $ColorType = ColorType::orderBy('OrderNo')
                ->get();
            return datatables()->of($ColorType)
                ->make(true);
        }
//       if(request()->ajax()){
//       return datatables()->of(ColorType::query())->make(true);
//       }
        $title = t('List of Colors');
        return view('backend.colors.index',compact('title'));
    }

    public function edit($id)
    {
        $color =  ColorType::findOrFail($id);
        $title = t('Edit Color',['username'=>$color->Name]);
        return view('backend.colors.form',compact('color','title'));
    }


    public function update($id)
    {
           $rules = [
            'Name'           => 'required|max:191',
            'Status'         => 'required',
            'OrderNo'         => 'max:255|integer',

           ];
        $data =  $this->request->validate($rules);
        $color =  ColorType::findOrFail($id);
        $color->update($data);
        return redirect()->route('colors')->with('success',t('successfully edited'));
    }

   public function create()
    {
         $title = t('Add New Color');
        return view('backend.colors.form',compact('title'));
    }


    public function store()
    {
           $rules = [
            'Name'         => 'required|max:191',
            'Status'       => 'required',
               'OrderNo'         => 'required|max:255|integer',
           ];

        $data =  $this->request->validate($rules);
        $test=ColorType::where('OrderNo','=',request()->input('OrderNo'))->first();
        if($test){
            return redirect()->route('colors')->with('error',t('القيمة محجوزة '));
        }else{
            $color = ColorType::create($data);

            return redirect()->route('colors')->with('success',t('successfully added'));
        }

    }

    public function destroy($id)
    {
        $colors = ColorType::with(['color_type_Id'])->find($id);
        $colors->color_type_Id()
            ->update(['ColorTypeId' => '1']);
        $colors->delete();
        return redirect()->route('colors')->with('success',t('successfully edited'));
    }

    public function all_delete()
    {
        $color =  ColorType::select()->all()->delete();
        return redirect()->route('colors')->with('success',t('successfully edited'));
    }


}
