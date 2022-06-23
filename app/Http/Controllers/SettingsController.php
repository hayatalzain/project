<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;


class SettingsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::get();
        $options = new \stdClass();

        foreach($settings as $setting){
         $options->{$setting->SettingKey} = $setting->SettingValue;
        }
        // ContactUs_Email
        // ContactUs_Phone
        // Max_Block_Payment
        // Max_Hold_Payment
        // Notification_Message
        // OrderExpiry
        // Payment_Details
        // tech_ContactUs_Email
        // tech_ContactUs_Phone
        // Technical_Commition
        return view('backend.settings',compact('options'))->with('settings',$settings);

    }


    public function save()
    {
         $posts = $this->request->except('_method','_token');

         foreach ($posts as $key => $value) {

           Setting::where('SettingKey',$key)->update(['SettingValue'=>$value]);

          }
//dd($posts);
        cache()->forget('settings');
        return redirect('/')->with('success',t('Successfully Updated'))->with('posts',$posts);

    }



}
