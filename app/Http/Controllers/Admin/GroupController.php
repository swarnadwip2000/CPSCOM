<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\User;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $groups = app('firebase.firestore')->database()->collection('groups')->where('id','=',$group_id)->documents();
       
       return view('admin.group.view-chat')->with(compact('chats','groups'));
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
        $data1 = app('firebase.firestore')->database()->collection('users')->where('isSuperAdmin','=',false)->documents();
        $users = $data1->rows();

        $data2 = app('firebase.firestore')->database()->collection('users')->where('isAdmin','=',true)->where('isSuperAdmin','=',false)->documents();
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

        $group = new Group();
        $group->group_id = $uid;
        $image = app('firebase.firestore')->database()->collection('groups')
        ->where('id', '=', $uid)
        ->documents();

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
            ]);

            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $image_path = $request->file('image')->store('group', 'public');
            $group->profile_picture = $image_path;
        }
        $group->save();

        for ($i=0; $i <=count($request->user_id) ; $i++) { 

            if ($i == count($request->user_id)) {
                $getUser = app('firebase.firestore')->database()->collection('users')->where('uid','=',$request->admin_id)->documents();
                $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                $members[$i]['isAdmin'] = true;
                $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                $members[$i]['profile_picture'] = $group->profile_picture ?? '';
            }else {
                $getUser = app('firebase.firestore')->database()->collection('users')->where('uid','=',$request->user_id[$i])->documents();
                $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                $members[$i]['isAdmin'] = false;
                $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                $members[$i]['profile_picture'] = $group->profile_picture ?? '';
            }
        }

        $count = count($request->user_id);
        $admin_members[$count+1]['email'] = Auth::user()->email;
        $admin_members[$count+1]['isAdmin'] = true;
        $admin_members[$count+1]['name'] = Auth::user()->name;
        $admin_members[$count+1]['uid'] = Auth::user()->uid;
        $admin_members[$count+1]['profile_picture'] = $group->profile_picture ?? '';

        // app('firebase.firestore')->database()->collection('users')->document($request->admin_id)->collection('groups')->document($uid)->set([
        //     'id'=>$uid,
        //     'name' => $request->name,
        //     'profile_picture'=>$group->profile_picture,
        //     //set created at timestamp
        //     'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'userId' => $request->admin_id,
        //     'group_description' => $request->description ?? '',
        // ]);

        $all_members = array_merge($members,$admin_members);
        foreach ($all_members as $key => $value) {
            // dd($value);
            app('firebase.firestore')->database()->collection('users')->document($value['uid'])->collection('groups')->document($uid)->set([
                    'id'=>$uid,
                    'name' => $request->name,
                    'profile_picture'=>$value['profile_picture'] ?? '',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'userId' => $request->admin_id,
                    'group_description' => $request->description ?? '',
                    'members' => $all_members,
                ]);
        }

        $data = app('firebase.firestore')->database()->collection('groups')->document($uid);
        $data->set([
            'id'=>$uid,
            'name' => $request->name,
            'group_description' => $request->description,
            'members'=>$all_members,
            'profile_picture'=> $group->profile_picture,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
            
       
        return redirect()->route('group.index')->with('message', 'Group has been created successfully.');
    }


    public function edit($id)
    {
        $groups = app('firebase.firestore')->database()->collection('groups')->where('id','=',$id)->documents();
        // dd($groups->rows()[0]->data()['members']);
        $data2 = app('firebase.firestore')->database()->collection('users')->where('isAdmin','=',true)->where('isSuperAdmin','=',false)->documents();
        $admins = $data2->rows();

        $data1 = app('firebase.firestore')->database()->collection('users')->where('isSuperAdmin','=',false)->documents();
        $users = $data1->rows();
        // dd($groups->rows()[0]->data()['profile_picture']);
        // dd($groups->rows()[0]->data()['members']);
        return view('admin.group.edit',compact('groups','admins','users'));
    }

    public function update(Request $request)
    {
        
        $request->validate([
            'name' => 'required',
            'user_id' => 'required',
            'admin_id' => 'required',
            
        ],[
            'admin_id.required'=>'Please select a admin.',
            'user_id.required'=>'Please select atleast one user.'
        ]);
        
        // delete group from user table firebase
        $image = app('firebase.firestore')->database()->collection('groups')
        ->where('id', '=', $request->group_id)
        ->documents();
        // dd($image->rows()[0]->data()['members']);
        if($image->rows()[0]->data()['members'] != null) {
        foreach ($image->rows()[0]->data()['members'] as $key => $value) {
            app('firebase.firestore')->database()->collection('users')->document($value['uid'])->collection('groups')->document($request->group_id)->delete();
         }
        } 

        // update group members
        $members = [];
        $uid = $request->group_id;
        $group = Group::where('group_id',$uid)->first();
        $image = app('firebase.firestore')->database()->collection('groups')
        ->where('id', '=', $uid)
        ->documents();

        if ($request->hasFile('image')) {
            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $image_path = $request->file('image')->store('group', 'public');
            $group->profile_picture = $image_path;
        }
        $group->save();

        for ($i=0; $i <=count($request->user_id) ; $i++) { 

            if ($i == count($request->user_id)) {
                $getUser = app('firebase.firestore')->database()->collection('users')->where('uid','=',$request->admin_id)->documents();
                $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                $members[$i]['isAdmin'] = true;
                $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                $members[$i]['profile_picture'] = $group->profile_picture ?? '';
                
            }else {
                $getUser = app('firebase.firestore')->database()->collection('users')->where('uid','=',$request->user_id[$i])->documents();
                $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                $members[$i]['isAdmin'] = false;
                $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                $members[$i]['profile_picture'] = $group->profile_picture ?? '';
            }
        }

        $count = count($request->user_id);
        $admin_members[$count+1]['email'] = Auth::user()->email;
        $admin_members[$count+1]['isAdmin'] = true;
        $admin_members[$count+1]['name'] = Auth::user()->name;
        $admin_members[$count+1]['uid'] = Auth::user()->uid;
        $admin_members[$count+1]['profile_picture'] = $group->profile_picture ?? '';   

        $all_members = array_merge($members,$admin_members);
        foreach ($all_members as $key => $value) {
            app('firebase.firestore')->database()->collection('users')->document($value['uid'])->collection('groups')->document($uid)->set([
                    'id'=>$uid,
                    'name' => $request->name,
                    'profile_picture'=>$value['profile_picture'] ?? '',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'userId' => $request->admin_id,
                    'group_description' => $request->description ?? '',
                    'members' => $all_members,
                ]);
        }


        $data = app('firebase.firestore')->database()->collection('groups')->document($uid);
        $data->update([
            ['path' => 'members', 'value' => $all_members],
            ['path' => 'name', 'value' => $request->name],
            ['path' => 'profile_picture', 'value' => $group->profile_picture],
            ['path' => 'group_description', 'value' => $request->description],
            ['path' => 'created_at', 'value' => Carbon::now()->format('Y-m-d H:i:s')],
        ]);
            
        return redirect()->route('group.index')->with('message', 'Group Details has been updated!!');
    }

    public function members($id)
    {
       
        $data  = app('firebase.firestore')->database()->collection('groups')->where('id','=',$id)->documents();;
        $members = $data->rows();
        // dd($members[0]->data()['id']);
        $groupId = $id;
        $users = app('firebase.firestore')->database()->collection('users')->where('isAdmin','=',false)->documents();
        // dd($members[0]['members'][]);
        return view('admin.group.member',compact('members','groupId','users'));
    }

    public function groupMemberStore(Request $request)
    {
        $request->validate([
            'add_member' => 'required',
        ],[
            'add_member.required'=>'Please select atleast one user.'
        ]);
            //    add a new member in group table firebase
        $group = app('firebase.firestore')->database()->collection('groups')
        ->where('id', '=', $request->group_id)
        ->documents();
        foreach ($group->rows()[0]->data()['members'] as $key => $value) {
            // echo $value['uid'] .'<br>';
            // echo $request->add_member;die;
            if ($value['uid'] == $request->add_member) {
                return redirect()->back()->with('error', 'User already added in this group!!');
            } 
        }

              $count = count($group->rows()[0]->data()['members']);
               $user = app('firebase.firestore')->database()->collection('users')->where('uid','=',$request->add_member)->documents();
                $new_members[$count+1]['email'] = $user->rows()[0]->data()['email'];
                $new_members[$count+1]['isAdmin'] = false;
                $new_members[$count+1]['name'] = $user->rows()[0]->data()['name'];
                $new_members[$count+1]['uid'] = $request->add_member;
                $new_members[$count+1]['profile_picture'] = $group->rows()[0]->data()['profile_picture'] ?? '';

                $old_members = $group->rows()[0]->data()['members'];
                $data = app('firebase.firestore')->database()->collection('groups')->document($request->group_id);
                $data->update([
                    ['path' => 'members', 'value' => array_merge($old_members,$new_members)],
                ]);
                app('firebase.firestore')->database()->collection('users')->document($request->add_member)->collection('groups')->document($request->group_id)->set([
                    'id'=>$request->group_id,
                    'name' => $group->rows()[0]->data()['name'],
                    'profile_picture'=>$group->rows()[0]->data()['profile_picture'],
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'userId' => $request->admin_id,
                    'group_description' => $group->rows()[0]->data()['group_description'] ?? '',
                    'members' => array_merge($old_members,$new_members),
                ]);
                return redirect()->back()->with('message', 'User added in this group!!');
    }

    public function groupMemberDelete($user_id, $group_id)
    {
        $group = app('firebase.firestore')->database()->collection('groups')
        ->where('id', '=', $group_id)
        ->documents();
        $members = $group->rows()[0]->data()['members'];
        foreach ($members as $key => $value) {
            if ($value['uid'] == $user_id) {
                unset($members[$key]);
            }
        }
        $data = app('firebase.firestore')->database()->collection('groups')->document($group_id);
        $data->update([
            ['path' => 'members', 'value' => $members],
        ]);
        app('firebase.firestore')->database()->collection('users')->document($user_id)->collection('groups')->document($group_id)->delete();
        return redirect()->back()->with('message', 'User removed from this group!!');
    }

    // get users for group
    public function getUsers(Request $request)
    {
        if($request->ajax())
        {
            $output="";
            $users = app('firebase.firestore')->database()->collection('users')->where('isSuperAdmin','=',false)->documents();
            if($users)
            {
                foreach ($users as $key => $user) {
                    if($user->data()['uid'] != $request->admin_id)
                    // select user who is not admin (select option)
                    {
                        $output.='<option value="'.$user->data()['uid'].'">'.$user->data()['name'].'</option>';   
                        
                    } else {
                        // select user who is admin (disabled option)
                        $output.='<option value="'.$user->data()['uid'].'" disabled>'.$user->data()['name'].'</option>';
                    }
                    
                }
                return Response($output);
            }
        }
    }

    public function editGetUsers(Request $request)
    {
       if ($request->ajax()) {
            $output="";
            $users = app('firebase.firestore')->database()->collection('users')->where('isSuperAdmin','=',false)->documents();
            // get group by group id from request
            $group = app('firebase.firestore')->database()->collection('groups')->where('id','=',$request->group_id)->documents();
            // get group members
            $members = $group->rows()[0]->data()['members'];
            
            if($users)
            {
                foreach ($users as $key => $user) {
                    if($user->data()['uid'] != $request->admin_id)
                    // select user who is not admin (select option)
                    {
                        $output.='<option value="'.$user->data()['uid'].'"';
                        if($members != null){
                        foreach ($members as $key => $value) {
                            if ($value['uid'] == $user->data()['uid'] && $value['isAdmin'] == false) {
                                $output.='selected';
                            }
                        } 
                    }
                        $output.= '>'.$user->data()['name'].'</option>';
                        
                    } else {
                        // select user who is admin (disabled option)
                        $output.='<option value="'.$user->data()['uid'].'" disabled>'.$user->data()['name'].'</option>';
                    }
                    
                }
                return Response($output);
            }
       }
    }
}