<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Issue;


class IssuesController extends Controller
{

    public function index()
    {
        if(request()->ajax()){
            $Issues = Issue::orderBy('OrderNo')->get();
            return datatables()->of($Issues)
                ->make(true);
        }

        $title = t('List of Issues');
        return view('backend.issues.index',compact('title'));
    }

    public function edit($id)
    {
        $issue =  Issue::findOrFail($id);
        $title = t('Edit Issue',['username'=>$issue->NameAr]);
        return view('backend.issues.form',compact('issue','title'));
    }


    public function update($id)
    {
           $rules = [
            'NameAr'         => 'required|max:191',
            'NameEn'         => 'required',
            'IssueStatusId' => 'required',
            'OrderNo'         => 'max:255|integer',
               ];

        $data =  $this->request->validate($rules);
        $issue =  Issue::findOrFail($id);
        $issue->update($data);
        return redirect()->route('issues')->with('success',t('successfully edited'));
 }

   public function create()
    {
         $title = t('Add New Issue');
        return view('backend.issues.form',compact('title'));
    }


    public function store()
    {
           $rules = [
            'NameAr'         => 'required|max:191',
            'NameEn'         => 'required',
            'IssueStatusId' => 'required|integer',
            'OrderNo'         => 'required|max:255|integer',
           ];

        $data =  $this->request->validate($rules);
        $test=Issue::where('OrderNo','=',request()->input('OrderNo'))->first();
        if($test){
            return redirect()->route('issues')->with('error',t('القيمة محجوزة '));
        }else {
        $data['CreatedBy'] = 'admin';
        $issue = Issue::create($data);

        return redirect()->route('issues')->with('success',t('successfully added'));
    }
    }

    public function destroy($id)
    {
           $issue =  Issue::destroy($id);
           return redirect()->route('issues')->with('success',t('successfully edited'));
    }


  public function ExportAllIssues(){

        $issues =  Issue::with('status')
            ->latest('CreationDate')->get();

            // Define the Excel spreadsheet headers
        $issue_data[] = ['#','إسم المشكلة', 'الحالة' , 'تاريخ التسجيل'];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($issues as $k => $issue) {

            $issue_data[++$k]['id'] = $issue->Id;
            $issue_data[$k]['name']     = $issue->NameAr;
            $issue_data[$k]['status']     = t($issue->status->Name);
            $issue_data[$k]['CreationDate'] = $issue->CreationDate;

        }
         //  return $issue_data;
        // Generate and return the spreadsheet
        \Excel::create('Issues', function($excel) use ($issue_data) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Orders');
            $excel->setCreator('Laravel')->setCompany('Halenaha');
            $excel->setDescription('Orders List');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($issue_data) {
                $sheet->fromArray($issue_data, null, 'A1', false, false);
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
