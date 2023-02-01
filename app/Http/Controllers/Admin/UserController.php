<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;                                                                                                                                                                                                                                                        
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;
use Kreait\Firebase\Factory;

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
        try {
            $name = $request->name;
            $email = $request->email;
            $password = $request->password;
            $newUser = $this->auth->createUserWithEmailAndPassword($email, $password);
            
            $data = app('firebase.firestore')->database()->collection('users')->newDocument();
            $data->set([
                'name'=>$request->name,
                'email'=>$request->email,
                'status'=>'Unavalible',
                'uid'=>$newUser->uid,
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
            $updatedUser = app('firebase.auth')->disableUser($id);
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
        $user['auth'] = $auth->getUser($request->id);
        $data = app('firebase.firestore')->database()->collection('users')->documents(); 
            foreach ($data as $key => $value) {
               if ($value->data()['uid'] == $request->id) {
                $user['value'] = app('firebase.firestore')->database()->collection('users')->document($value->id())->snapshot(); 
               }
            }
        return response()->json(['user'=>$user, 'message'=>'User details found successfully.']);
    }
}
