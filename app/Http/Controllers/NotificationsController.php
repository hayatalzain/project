<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingNotification;
use DB,Auth;

class NotificationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       if(request()->ajax()){
       return datatables()->of(MarketingNotification::query())->make(true);
       }
         $title = 'أرشيف الإشعارات الترويجية';
        return view('backend.notifications.index',compact('title'));

    }

    public function edit($id)
    {
        $title = 'تعديل نص الإشعار ';
        $notification = MarketingNotification::where(['id' =>$id ,'IsPublished'=>false])->firstOrFail();
        return view('backend.notifications.form',compact('title','notification'));
    }


    public function update($id)
    {

     $notification = MarketingNotification::where(['id' =>$id ,'IsPublished'=>false])->first();
      if(!$notification)
       return redirect()->route('notifications')->with('error',' تم نشر الرسالة مسبقاً  !');


       $data = $this->request->validate(
        [
         'Title'   => 'required|max:50',
         'Message' => 'required|max:255',
        ]);



      $data['IsPublished'] = false;
      $data['UpdatedBy'] = Auth::user()->username;
      $notification->update($data);



       return redirect()->route('notifications')->with('success','تم   تحديث   الإشعار بنجاح !');
    }



    public function publish($id)
    {

     $notification = MarketingNotification::where(['id' =>$id ,'IsPublished'=>false])->first();
      if(!$notification)
       return redirect()->route('notifications')->with('error',' تم نشر الرسالة مسبقاً  !');


    DB::select(DB::raw("exec [dbo].[PublishMarketingNotifications] :marketingNotificationId"),[
            ':marketingNotificationId' => $id,
            ]);


       return redirect()->route('notifications')->with('success','تم  إرسال   الإشعار بنجاح !');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'إرسال إشعار ترويجي';

        return view('backend.notifications.form',compact('title'));
    }


    public function store()
    {

       $data = $this->request->validate(
        [
         'Title' => 'required|max:50',
         'Message' => 'required|max:255',
        ]);
      $data['IsPublished'] = false;
      $data['CreatedBy'] = Auth::user()->username;
      $notification = MarketingNotification::create($data);


       return redirect()->route('notifications')->with('success','تم  حفظ  الإشعار بنجاح !');
    }


    public function destroy($id)
    {
      $notification = MarketingNotification::destroy($id);

       return back()->with('success','تم  حذف  الإشعار بنجاح !');
    }

}
