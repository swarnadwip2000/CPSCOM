<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\User;

class GroupController extends Controller
{
    //
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
       
        $users = app('firebase.firestore')->database()->collection('users');
        $query = $users->where('isAdmin', '=', false);
        $documents = $query->documents();
        $allUsers = $documents->rows();
        foreach($allUsers as $data) { 
        if($data->exists()){            
            $users_id = $data['uid']; 
            $document = app('firebase.firestore')->database()
                        ->collection('users')
                        ->document($users_id)
                        ->collection('groups')
                        ->documents();
            }   
        }
       
        $groups = $document->rows();
        return view('admin.group.list',compact('groups'));
    }

    public function viewChat($id)
    {
        // return $id;
        $groups = app('firebase.firestore')->database()
                    ->collection('groups')
                    ->document($id)
                    ->collection('chats')
                    ->documents();
        $allUsers = $groups->rows();
        dd($allUsers);


    }
}
