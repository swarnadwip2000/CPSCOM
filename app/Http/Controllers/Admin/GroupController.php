<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\User;
use App\Models\Group;

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
        // return $id;
       $dat = app('firebase.firestore')->database()
                    ->collection('users')
                    ->documents();
        foreach ($dat as $key => $value) {
              $ss =  app('firebase.firestore')->database()
                ->collection('users')
                ->document($value->data()['uid'])
                ->collection('groups')
                ->documents();
            if (!$ss->isEmpty()) {
                $ss =  app('firebase.firestore')->database()
                ->collection('users')
                ->document($value->data()['uid'])
                ->collection('groups')
                ->document($id)
                ->delete();
            }
            // dd($ss);
        }

        $delete_chat = app('firebase.firestore')->database()
        ->collection('groups')
        ->document($id)
        ->delete();
    return redirect()->back()->with('error', 'Group has been deleted!!');
    }

    public function groupImageUpdate($id)
    {
        $group_id = $id;
        $group = Group::where('group_id',$id)->first();
        return view('admin.group.image-upload')->with(compact('group','group_id'));                                                                                                                                                     
    }

    public function groupImageUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
        ]);

         $count = Group::where('group_id',$request->group_id)->count();
        if($count > 0){

            $group = Group::where('group_id',$request->group_id)->first();
            if ($request->hasFile('image')) {
                $file= $request->file('image');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('image')->store('group', 'public');
                $group->profile_picture = $image_path;
            }
            $group->save();

        }else{
            
            $group = new Group();
            $group->group_id = $request->group_id;
            
            if ($request->hasFile('image')) {
                $file= $request->file('image');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('image')->store('group', 'public');
                $group->profile_picture = $image_path;
            }
            $group->save();
        }
        return redirect()->back()->with('success', 'Group Image has been updated!!');
    }
}
