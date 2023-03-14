<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;
class ProfileController extends Controller
{

    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)
                    ->withServiceAccount(__DIR__.'/firebase_credential.json')
                    ->withDataBaseUri('https://cpscom-acb3c.firebaseio.com');
        $this->auth = $factory->createAuth();
    }

    public function index()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . Auth::user()->id,
        ]);

        $properties =[
            'displayName' => $request->name,
            'email' => $request->email,
        ];

        $data = User::find(Auth::user()->id);
        $data->name = $request->name;
        $data->email = $request->email;

        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);

            $file = $request->file('profile_picture');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $image_path = $request->file('profile_picture')->store('admin', 'public');
            $data->profile_picture = $image_path;
        }
        $data->save();

         $this->auth->updateUser($data->uid, $properties);
         app('firebase.firestore')->database()->collection('users')->document($data->uid)
                ->update([
                    ['path' => 'name', 'value' => $request->name],
                    ['path' => 'email', 'value' => $request->email],
                ]);

                if ($request->hasFile('profile_picture')) {
                    app('firebase.firestore')->database()->collection('users')->document($data->uid)
                ->update([
                    ['path' => 'profile_picture', 'value' => $image_path],
                ]);
                
            }

        return redirect()->back()->with('message', 'Profile updated successfully.');
    }

    public function password()
    {
        return view('admin.password');
    }

    public function passwordUpdate(Request $request)
    {

        $request->validate([
            'old_password' => 'required|min:8|password',
            'new_password' => 'required|min:8|different:old_password',
            'confirm_password' => 'required|min:8|same:new_password',

        ], [
            'old_password.password' => 'Old password is not correct',
        ]);
        $properties =[
            'password' => $request->new_password,
        ];
        $data = User::find(Auth::user()->id);
        $data->password = Hash::make($request->new_password);
        $data->update();
        $this->auth->updateUser($data->uid, $properties);
        return redirect()->back()->with('message', 'Password updated successfully.');
    }
}
