<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;
use Kreait\Firebase\Factory;

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
            
            $data = app('firebase.firestore')->database()->collection('users')->newDocument();
            $data->set([
                'name'=>$request->name,
                'email'=>$request->email,
                'status'=>'Unavalible',
                'uid'=>$createdUser->uid,
                'isAdmin'=> true
            ]);
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
        $admin = $auth->getUser($request->id);
        return response()->json(['admin'=>$admin, 'message'=>'Admin details found successfully.']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'edit_name' => 'required',
            'edit_email' => 'required',
        ]);
        $properties =[
            'displayName' => $request->edit_email,
            'email' => $request->edit_email,
          ];
          $updatedUser = $this->auth->updateUser($request->id, $properties);
          $data = app('firebase.firestore')->database()->collection('users')->documents(); 
            foreach ($data as $key => $value) {
               if ($value->data()['uid'] == $request->id) {
                $admin = app('firebase.firestore')->database()->collection('users')->document($value->id())
                        ->update([
                            ['path' => 'name', 'value' => $request->edit_name],
                            ['path' => 'email', 'value' => $request->edit_email],
                        ]);
               }
            }
          return redirect()->back()->with('message',  'Admin account has been successfully updated.');
    }
}
