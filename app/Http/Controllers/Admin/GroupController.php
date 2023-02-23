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
        $image = app('firebase.firestore')->database()->collection('groups')
                ->where('id', '=', $request->group_id)
                ->documents();
        
         $count = Group::where('group_id',$request->group_id)->count();
        if($count > 0){
            $group = Group::where('group_id',$request->group_id)->first();
            
            if ($request->hasFile('image')) {
                $file= $request->file('image');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('image')->store('group', 'public');
                $group->profile_picture = $image_path;

                foreach ($image->rows()[0]->data()['members'] as $key => $value) {
                    app('firebase.firestore')->database()->collection('users')->document($value['uid'])->collection('groups')->document($request->group_id)->update([
                        ['path' => 'profile_picture', 'value' => $image_path]
                    ]);
                }
            }
            $group->save();

        }else{
            
            $group = new Group();
            $group->group_id = $request->group_id;
            $image = app('firebase.firestore')->database()->collection('groups')
            ->where('id', '=', $request->group_id)
            ->documents();

            if ($request->hasFile('image')) {
                $file= $request->file('image');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('image')->store('group', 'public');
                $group->profile_picture = $image_path;
                
                foreach ($image->rows()[0]->data()['members'] as $key => $value) {
                    app('firebase.firestore')->database()->collection('users')->document($value['uid'])->collection('groups')->document($request->group_id)->update([
                        ['path' => 'profile_picture', 'value' => $image_path]
                    ]);
                }
            }
            $group->save();
        }
        return redirect()->back()->with('success', 'Group Image has been updated!!');
    }

    public function create()
    {
        $data1 = app('firebase.firestore')->database()->collection('users')->where('isAdmin','=',false)->documents();
        $users = $data1->rows();

        $data2 = app('firebase.firestore')->database()->collection('users')->where('isAdmin','=',true)->documents();
        $admins = $data2->rows();
        return view('admin.group.create')->with(compact('users','admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'admin_id' => 'required',
            'user_id' => 'required',
            'name' => 'required',
        ],[
            'admin_id.required'=>'Please select a admin.',
            'user_id.required'=>'Please select a user.'
        ]);
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $uid = substr(str_shuffle(str_repeat($pool, 36)), 0, 36);
        $members = [];
        for ($i=0; $i <=count($request->user_id) ; $i++) { 
            
            if ($i == count($request->user_id)) {
                $getUser = app('firebase.firestore')->database()->collection('users')->where('uid','=',$request->admin_id)->documents();
                $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                $members[$i]['isAdmin'] = true;
                $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                app('firebase.firestore')->database()->collection('users')->document($request->admin_id)->collection('groups')->document($uid)->set([
                    'id'=>$uid,
                    'name' => $request->name,
                    'profile_picture'=>'',
                ]);
            }else {
                $getUser = app('firebase.firestore')->database()->collection('users')->where('uid','=',$request->user_id[$i])->documents();
                $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                $members[$i]['isAdmin'] = false;
                $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                app('firebase.firestore')->database()->collection('users')->document($request->user_id[$i])->collection('groups')->document($uid)->set([
                    'id'=>$uid,
                    'name' => $request->name,
                    'profile_picture'=>'',
                ]);
            }
        }
        $data = app('firebase.firestore')->database()->collection('groups')->document($uid);
            $data->set([
                'id'=>$uid,
                'name' => $request->name,
                'members'=>$members,
            ]);
        return redirect()->route('group.index')->with('message', 'Group has been created successfully.');
    }
}
