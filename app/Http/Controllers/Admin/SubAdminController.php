<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;
use Kreait\Firebase\Factory;
use App\Models\User;

class SubAdminController extends Controller
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
        $data = app('firebase.firestore')->database()->collection('users')->documents();  
        $admins = $data->rows();
        //  dd($admins);
        return view('admin.sub-admin.list')->with(compact('admins'));
    }


    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'email' => 'required',
        ]);
        try {
            $name = $request->name;
            $email = $request->email;
            $password = $request->password;
            $userProperties = [
                'email' => $email,
                'password' => $request->password,
                'displayName' => $name,
             ];
             $createdUser = $this->auth->createUser($userProperties);
            
             $user = new User;
             $user->uid =  $createdUser->uid;
             $user->name =  $request->name;
             $user->email =  $request->email;
             $user->password =  bcrypt($request->password);
             if ($request->hasFile('profile_picture')) {
                $file= $request->file('profile_picture');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('profile_picture')->store('user', 'public');
                $user->profile_picture = $image_path;
            }
            $user->save();

            $data = app('firebase.firestore')->database()->collection('users')->document($createdUser->uid);
            $data->set([
                'email'=>$request->email,
                'status'=>'Unavalible',
                'name'=>$request->name,
                'uid'=>$createdUser->uid,
                'isAdmin'=>true,
            ]);

            if ($request->hasFile('profile_picture')) {
                $data = app('firebase.firestore')->database()->collection('users')->document($createdUser->uid);
                $data->set([
                    'profile_picture' => $image_path,
                ], ['merge' => true]);
            }

            $maildata = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ];
    
            Mail::to($request->email)->send(new RegistrationMail($maildata));
            return redirect()->back()->with('message', 'Admin account has been successfully created.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error',  $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            User::where('uid', $id)->delete();
            $updatedUser = app('firebase.auth')->deleteUser($id);
            $data = app('firebase.firestore')->database()->collection('users')->documents(); 
            foreach ($data as $key => $value) {
               if ($value->data()['uid'] == $id) {
                $data = app('firebase.firestore')->database()->collection('users')->document($value->id())->delete();
               }
            }
            return redirect()->back()->with('error',  'Admin account has been deleted!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error',  $th->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $auth = app('firebase.auth');
        $admin['detail'] = $auth->getUser($request->id);
        $admin['profile'] = User::where('uid', $request->uid)->first();
        return response()->json(['admin'=>$admin, 'message'=>'Admin details found successfully.']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'edit_name' => 'required',
            'edit_email' => 'required',
        ]);
        $properties =[
            'displayName' => $request->edit_name,
            'email' => $request->edit_email,
          ];
          
            $count = User::where('uid', $request->id)->count();
            if($count > 0) {
                $user = User::where('uid', $request->id)->first();
            } else {
                $user = new User();
                $user->uid =  $request->id;
            }
            
            $user->name =  $request->edit_name;
            $user->email =  $request->edit_email;
            if ($request->hasFile('profile_picture')) {
                $file= $request->file('profile_picture');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('profile_picture')->store('user', 'public');
                $user->profile_picture = $image_path;
            }
            $user->save();

            $updatedUser = $this->auth->updateUser($request->id, $properties);
            $data = app('firebase.firestore')->database()->collection('users')->documents(); 
                foreach ($data as $key => $value) {
                if ($value->data()['uid'] == $request->id) {
                    $admin = app('firebase.firestore')->database()->collection('users')->document($value->id())
                            ->update([
                                ['path' => 'name', 'value' => $request->edit_name],
                                ['path' => 'email', 'value' => $request->edit_email],
                                ['path' => 'profile_picture', 'value' => $image_path],
                            ]);
                    }
            }

          return redirect()->back()->with('message',  'Admin account has been successfully updated.');
    }
}
