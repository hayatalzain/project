<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Terms;
use DataTables;

class TermsController extends Controller
{

    public function index()
    {
        if(request()->ajax()){
            return datatables()->of(Terms::query())->make(true);
        }
        $title = t('List of terms');
        return view('backend.terms.index',compact('title'));
    }

    public function edit($id)
    {
        $terms =  Terms::findOrFail($id);
        $title = t('Edit terms',['username'=>$terms->Name]);
        return view('backend.terms.form',compact('terms','title'));
    }

    public function update($id)
    {
        $rules = [
            'Name'         => 'required|max:50',
            'Note'         => 'required|max:299',
        ];
        $data =  $this->request->validate($rules);
        $terms =  Terms::findOrFail($id);
        $terms->update($data);
        return redirect()->route('terms')->with('success',t('successfully edited'));
    }

   public function create()
    {
         $title = t('Add New terms');
        return view('backend.terms.form',compact('title'));
    }

    public function store()
    {
           $rules = [
               'Name'         => 'required|max:50',
               'Note'         => 'required|max:299',
          ];

        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = 'admin';
        $termsID = Terms::create($data);

        return redirect()->route('terms')->with('success',t('successfully added'));

    }

    public  function show(){

    }
    public function destroy($id)
    {
        $terms =  Terms::where('id', $id)->delete();

           return redirect()->route('terms')->with('success',t('successfully deleted'));
    }


}
