<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;

class AdminController extends Controller
{
    //
    public function index()
    {
        $admins = User::role('ADMIN')->get();
        return view('admin.admin.list')->with(compact('admins'));
    }

    public function store(Request $request)
    {
        $admin = new User;  
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);
        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            
            $file= $request->file('profile_picture');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $image_path = $request->file('profile_picture')->store('admin', 'public');
            $admin->profile_picture = $image_path;
        }
        $admin->save();
        $admin->assignRole('ADMIN');   
        $maildata = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];

        Mail::to($request->email)->send(new RegistrationMail($maildata));
        return redirect()->route('admin.index')->with('message', 'Admin has been added successfully');
    }

    public function edit($id)
    {
        $admin = User::where('id',$id)->first();
        return response()->json(['admin'=>$admin, 'message'=>'Admin details found successfully.']);
    }

    public function update(Request $request)
    {
        
        $request->validate([
            'edit_name' => 'required',
            'edit_email' => 'required',
        ]);
        
            $user = User::where('id', $request->id)->first();
           
            $user->name =  $request->edit_name;
            $user->email =  $request->edit_email;
            if ($request->hasFile('profile_picture')) {
                $file= $request->file('profile_picture');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('profile_picture')->store('user', 'public');
                $user->profile_picture = $image_path;
            }
            $user->save();
            
        return redirect()->back()->with('message',  'Admin account has been successfully updated.');
    }


    public function delete($id)
    {
        
        User::findOrFail($id)->delete();
        return redirect()->back()->with('error', 'Admin has been deleted!');
    }
}
