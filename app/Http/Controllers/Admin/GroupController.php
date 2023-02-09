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
        $groups = app('firebase.firestore')->database()->collection('groups')->documents();
        // dd($groups->rows());
        return view('admin.group.list',compact('groups'));
    }

    public function viewChat($id)
    {
        // return $id;
        $chats = app('firebase.firestore')->database()
                    ->collection('groups')
                    ->document($id)
                    ->collection('chats')
                    ->documents();
        // $chats = $group_chats->rows();
        // dd( $group_chats);
        $group_id = $id;
       return view('admin.group.view-chat')->with(compact('chats','group_id'));
    }

    public function chatDelete($chatId, $groupId)
    {
        $delete_chat = app('firebase.firestore')->database()
                        ->collection('groups')
                        ->document($groupId)
                        ->collection('chats')
                        ->document($chatId)
                    ->delete();
        // dd($delete_chat);
        return redirect()->back()->with('error', 'Chart has been deleted!!');
    }

    public function delete($id)
    {
        $delete_chat = app('firebase.firestore')->database()
        ->collection('groups')
        ->document($id)
        ->delete();
    return redirect()->back()->with('error', 'Group has been deleted!!');
    }
}
