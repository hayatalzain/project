<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Rules\CheckPassword;
use App\Models\Admin as User;
use App\Models\Permission;


class AdminsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       if(request()->ajax()){
       return datatables()->of(User::query())->make(true);
       }
        $title = t('List of Admins');
        return view('backend.admins.index',compact('title'));
    }

    public function edit($id)
    {

        $user =  User::findOrFail($id);
        $permissions = Permission::get();
        $title = t('Edit Account',['username'=>$user->username]);
        return view('backend.admins.form',compact('user','title','permissions'));
    }


    public function update($id)
    {
           $user =  User::findOrFail($id);
           $rules = [
            'full_name'    => 'required|max:191',
            'username'     => 'required|unique:sqlsrv.App.admins,username,'.$user->id,
            'email'        => 'sometimes|email|unique:sqlsrv.App.admins,email,'.$user->id,
            'password'     => 'sometimes|nullable|min:6',
            'status'       => 'required|boolean',
            'permissions'  => 'required|array',
        ];

        $data =  $this->request->validate($rules);
        $user->update($data);
        $user->permissions()->sync($data['permissions']);
        return redirect()->route('admins')->with('success',t('successfully edited'));

    }

   public function create()
    {
         $permissions = Permission::get();
         $title = t('Add New Account');
        return view('backend.admins.form',compact('title','permissions'));
    }


    public function store()
    {
           $rules = [
            'full_name'    => 'required|max:191',
            'username'     => 'required|unique:sqlsrv.App.admins,username',
            'email'        => 'sometimes|required|email|unique:sqlsrv.App.admins,email',
            'password'     => 'required|min:6',
            'status'       => 'required|boolean',
            'permissions'  => 'required|array',
        ];
        $data =  $this->request->validate($rules);
        $user = User::create($data);
        $user->permissions()->sync($data['permissions']);
        return redirect()->route('admins')->with('success',t('successfully added'));
    }

    public function destroy($id)
    {
           $user =  User::destroy($id);
           return redirect()->route('admins')->with('success',t('successfully deleted'));
    }

}
