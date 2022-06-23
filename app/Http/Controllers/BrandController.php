<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceIssue;
use App\Models\Issue;
use App\Models\Brand;
use DataTables;

class BrandController extends Controller
{

    public function index()
    {
        $brand = Brand::with('brand')->orderBy('OrderNo')
            ->get();
       if(request()->ajax()){
       return datatables()->of($brand)
       ->make(true);
       }

        $title = t('List of Brand');
        return view('backend.brand.index',compact('title'));
    }

    public function edit($id)
    {
        $brand =  Brand::findOrFail($id);
        $title = t('Edit Brand',['username'=>$brand->NameAr]);
        return view('backend.brand.form',compact('brand','title'));
    }

    public function update($id)
    {
           $rules = [
            'NameAr'         => 'required|max:191',
            'NameEn'         => 'required',
            'OrderNo'         => 'max:255|integer',
            'BrandStatusId' => 'required',
        ];

        $data =  $this->request->validate($rules);
        $brand =  Brand::findOrFail($id);
        $brand->update($data);
        return redirect()->route('brand')->with('success',t('successfully edited'));

    }

   public function create()
    {
         $title = t('Add New Brand');
        return view('backend.brand.form',compact('title'));
    }

    public function store()
    {
           $rules = [
            'NameAr'         => 'required|max:191',
            'NameEn'         => 'required',
//               'OrderNo.*' => ['required', 'max:255', 'integer', Rule::unique('Brand', 'OrderNo')],
//               'OrderNo'         => 'required|max:255|integer|unique:App.Brand,OrderNo',
               'OrderNo'         => 'required|max:255|integer',
               'BrandStatusId' => 'required|integer',
        ];

        $data =  $this->request->validate($rules);
            $test=Brand::where('OrderNo','=',request()->input('OrderNo'))->first();
        if($test){
            return redirect()->route('brand')->with('error',t('القيمة محجوزة '));
        }else{
        $data['CreatedBy'] = 'admin';
        $brandD = Brand::create($data);
        return redirect()->route('brand')->with('success',t('successfully added'));
        }
    }

    public  function show(){

    }

    public function destroy($id)
    {
           $brand =  Brand::where('id', $id)->delete();

           return redirect()->route('brand')->with('success',t('successfully deleted'));
    }


}
