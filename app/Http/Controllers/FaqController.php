<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;


class FaqController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       if(request()->ajax()){
       return datatables()->of(Faq::query())->make(true);
       }

        $title = t('Frequently asked questions');
        return view('backend.faq.index',compact('title'));
    }

    public function edit($id)
    {

        $faq =  Faq::findOrFail($id);
        $title = t('Edit Faq');
        return view('backend.faq.form',compact('faq','title'));
    }


    public function update($id)
    {
           $rules = [
            'Question' => 'required|max:191',
            'Answer'   => 'required',
            'IsActive' => 'required|boolean',
        ];

        $data  =  $this->request->validate($rules);
        $faq =  Faq::findOrFail($id);
        $faq->update($data);
        return redirect()->route('faq')->with('success',t('successfully edited'));

    }



   public function create()
    {
         $title = t('Add New Question');
        return view('backend.faq.form',compact('title'));
    }


    public function store()
    {

           $rules = [
            'Question' => 'required|max:191',
            'Answer'   => 'required',
            'IsActive' => 'required|boolean',
        ];

        $data =  $this->request->validate($rules);
        $faq = Faq::create($data);

        return redirect()->route('faq')->with('success',t('successfully added'));


    }



    public function destroy($id)
    {
           $faq =  Faq::destroy($id);
           return redirect()->route('faq')->with('success',t('successfully edited'));
    }





}
