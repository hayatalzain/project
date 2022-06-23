<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use Auth;

class OffersController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       if(request()->ajax()){
       return datatables()->of(Offer::query())->make(true);
       }
        $title = t('Offers List');
        return view('backend.offers.index',compact('title'));
    }

    public function edit($id)
    {

        $offer =  Offer::findOrFail($id);
        $title = t('Edit Offer');
        return view('backend.offers.form',compact('offer','title'));
    }

    public function update($id)
    {


        $offer =  Offer::findOrFail($id);

           $rules = [
            'Name' => 'required|max:191',
            'Code'   => 'required|unique:sqlsrv.App.Offers,Code,'.$offer->Id,
            'DiscountPercent'   => 'required',
            'IsActive' => 'required|boolean',
        ];

        $data  =  $this->request->validate($rules);
        $offer->update($data);
        return redirect()->route('offers')->with('success',t('successfully edited'));

    }

   public function create()
    {
         $title = t('Add New Offer');
        return view('backend.offers.form',compact('title'));
    }

    public function store()
    {

           $rules = [
            'Name' => 'required|max:191',
            'Code'   => 'required|unique:sqlsrv.App.Offers',
            'DiscountPercent'   => 'required',
            'IsActive' => 'required|boolean',
        ];

        $data =  $this->request->validate($rules);
        $data['CreatedBy'] = Auth::user()->username;
        $offer = Offer::create($data);

        return redirect()->route('offers')->with('success',t('successfully added'));


    }

    public function destroy($id)
    {
           $offer =  Offer::destroy($id);
           return redirect()->route('offers')->with('success',t('successfully edited'));
    }


    public function ExportOffersReport(){

        $search    = $this->request->get('q','');
        $model = new Offer;
        $valid_columns = $model->getFillable();

        $users  =  Offer::query()->Where(function($q)use($valid_columns,$search){
            foreach($valid_columns as $i => $field){
                if($i==0)
                    $q->where($field, 'like', "%" . $search . "%");
                else
                    $q->OrWhere($field, 'like', "%" . $search . "%");
            }
        })->get();


        // Define the Excel spreadsheet headers
        $user_data[] = ['م','الاسم ','الكود  ','نسبة الخصم ','تاريخ الانشاء  '];

        // Convert each member of the returned collection into an array,
        // and append it to the phones array.
        foreach ($users as $k => $user) {
            // if(!$user->orders_commision_paid and !$user->orders_commision_unpaid)
            //     continue;

            $user_data[++$k]['Id'] = $user->Id;
            $user_data[$k]['Name'] = $user->Name;
            $user_data[$k]['Code'] = $user->Code;
            $user_data[$k]['DiscountPercent'] = $user->DiscountPercent;
            $user_data[$k]['CreationDate'] = $user->CreationDate;

        }
        //  return $user_data;
        // Generate and return the spreadsheet
        \Excel::create('Technicals', function($excel) use ($user_data) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Technicals');
            $excel->setCreator('Laravel')->setCompany('Halenaha');
            $excel->setDescription('Technicals List');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($user_data) {
                $sheet->fromArray($user_data, null, 'A1', false, false);
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
