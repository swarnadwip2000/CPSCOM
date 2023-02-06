<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;                                                                                                                                                                                                                                                        
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;
use Kreait\Firebase\Factory;
use App\Models\User;

class UserController extends Controller
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
        $users = $data->rows();
        return view('admin.users.list')->with(compact('users'));
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

            //  dd($createdUser);
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

            $data = app('firebase.firestore')->database()->collection('users')->newDocument();
            $data->set([
                'name'=>$request->name,
                'email'=>$request->email,
                'status'=>'Unavalible',
                'uid'=>$createdUser->uid,
                'isAdmin' => false,
            ]);
            $maildata = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ];
    
            Mail::to($request->email)->send(new RegistrationMail($maildata));
            return redirect()->back()->with('message', 'User account has been successfully created.');
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
            return redirect()->back()->with('error',  'User account has been deleted!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error',  $th->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $auth = app('firebase.auth');
        $user['detail'] = $auth->getUser($request->id);
        $user['profile'] = User::where('uid', $request->uid)->first();
        
        return response()->json(['user'=>$user, 'message'=>'User details found successfully.']);
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
          $updatedUser = $this->auth->updateUser($request->id, $properties);
          $data = app('firebase.firestore')->database()->collection('users')->documents(); 
            foreach ($data as $key => $value) {
               if ($value->data()['uid'] == $request->id) {
                $user = app('firebase.firestore')->database()->collection('users')->document($value->id())
                        ->update([
                            ['path' => 'name', 'value' => $request->edit_name],
                            ['path' => 'email', 'value' => $request->edit_email],
                        ]);
               }
            }

            $user = User::where('uid', $request->id)->first();
            $user->name =  $request->edit_name;
            $user->email =  $request->edit_email;
            if ($request->hasFile('profile_picture')) {
               $file= $request->file('profile_picture');
               $filename= date('YmdHi').$file->getClientOriginalName();
               $image_path = $request->file('profile_picture')->store('user', 'public');
               $user->profile_picture = $image_path;
           }
           $user->save();

          return redirect()->back()->with('message',  'User account has been successfully updated.');
    }
}
