<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,'.Auth::user()->id,
        ]);

        $data = User::find(Auth::user()->id);
        $data->name = $request->name;
        $data->email = $request->email;
        if ($request->password) {
            $request->validate([
                'password' => 'required|min:8',
            ]);
            $data->password = bcrypt($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            
            $file= $request->file('profile_picture');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $image_path = $request->file('profile_picture')->store('admin', 'public');
            $data->profile_picture = $image_path;
        }
        $data->save();
        return redirect()->back()->with('message', 'Profile updated successfully.');
    }
}
