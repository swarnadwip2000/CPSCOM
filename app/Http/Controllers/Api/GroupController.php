<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Str;
class GroupController extends Controller
{
    public $successStatus = 200;
    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(__DIR__ . '/firebase_credential.json')
            ->withDataBaseUri('https://cpscom-acb3c.firebaseio.com');
        $this->auth = $factory->createAuth();
    }


    public function imageUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,  'error' => $errors], 401);
        }

        $image = app('firebase.firestore')->database()->collection('groups')
            ->where('id', '=', $request->group_id)
            ->documents();
        if (count($image->rows()) > 0) {
            $count = Group::where('group_id', $request->group_id)->count();
            if ($count > 0) {
                $group = Group::where('group_id', $request->group_id)->first();

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = date('YmdHi') . $file->getClientOriginalName();
                    $image_path = $request->file('image')->store('group', 'public');
                    $group->profile_picture = $image_path;

                    foreach ($image->rows()[0]->data()['members'] as $key => $value) {
                        app('firebase.firestore')->database()->collection('users')->document($value['uid'])->collection('groups')->document($request->group_id)->update([
                            ['path' => 'profile_picture', 'value' => $image_path]
                        ]);
                    }
                }
                $group->save();

                return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Image uploaded successfully'], 200);
            } else {
                $group = new Group();
                $group->group_id = $request->group_id;
                $image = app('firebase.firestore')->database()->collection('groups')
                    ->where('id', '=', $request->group_id)
                    ->documents();

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = date('YmdHi') . $file->getClientOriginalName();
                    $image_path = $request->file('image')->store('group', 'public');
                    $group->profile_picture = $image_path;

                    foreach ($image->rows()[0]->data()['members'] as $key => $value) {
                        app('firebase.firestore')->database()->collection('users')->document($value['uid'])->collection('groups')->document($request->group_id)->update([
                            ['path' => 'profile_picture', 'value' => $image_path]
                        ]);
                    }
                }
                $group->save();

                return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Image uploaded successfully'], 200);
            }
        } else {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'Group not found'], 401);
        }
    }

    public function members(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,  'error' => $errors], 401);
        }

        // group members with profile picture
        try {
            $members = app('firebase.firestore')->database()->collection('groups')
            ->where('id', '=', $request->id)
            ->documents();
        if (count($members->rows()) > 0) {
            $members = $members->rows()[0]->data()['members'];
            $data = [];
            foreach ($members as $key => $value) {
                $user = app('firebase.firestore')->database()->collection('users')
                    ->where('uid', '=', $value['uid'])
                    ->documents();
                if (count($user->rows()) > 0) {
                    $data[$key]['uid'] = $value['uid'];
                    $data[$key]['name'] = $user->rows()[0]->data()['name'];
                    $data[$key]['email'] =  $user->rows()[0]->data()['email'];
                    $data[$key]['isAdmin'] =  $value['isAdmin'];
                    $data[$key]['profile_picture'] = $user->rows()[0]->data()['profile_picture'] ?? null;
                }
            }
            return response()->json(['status' => true, 'statusCode' => 200, 'data' => $data], 200);
        } else {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'Group not found'], 401);
        }
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $th->getMessage() ], 401);
        }
    }

    public function createGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required',
            'uid.*' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,  'error' => $errors], 401);
        }

        // return $request->uid;

        try {
            // uid is not valid or not exist
            foreach ($request->uid as $key => $value) {
                $user = app('firebase.firestore')->database()->collection('users')
                    ->where('uid', '=', $value)
                    ->documents();
                if (count($user->rows()) == 0) {
                    return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'User not found'], 401);
                }
            }

            //  create group on firebase
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            $uid = substr(str_shuffle(str_repeat($pool, 36)), 0, 36);
            $group = new Group();
            $group->group_id = $uid;

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $image_path = $request->file('profile_picture')->store('group', 'public');
                $group->profile_picture = $image_path;
            }
            $group->save();
            // search isSuperadmin true from user
            $isSuperadmin =  app('firebase.firestore')->database()->collection('users')
                ->where('isSuperAdmin', '=', true)
                ->documents();
            $members = [];
            for ($i = 0; $i <= count($request->uid); $i++) {
                if (count($request->uid) > $i) {
                    $getUser = app('firebase.firestore')->database()->collection('users')->where('uid', '=', $request->uid[$i])->documents();
                    if ($i == 0) {
                        $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                        $members[$i]['isAdmin'] = true;
                        $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                        $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                        app('firebase.firestore')->database()->collection('users')->document($request->uid[$i])->collection('groups')->document($uid)->set([
                            'id' => $uid,
                            'name' => $request->group_name,
                            'profile_picture' => $group->profile_picture,
                        ]);
                    } else {
                        $members[$i]['email'] = $getUser->rows()[0]->data()['email'];
                        $members[$i]['isAdmin'] = false;
                        $members[$i]['name'] = $getUser->rows()[0]->data()['name'];
                        $members[$i]['uid'] = $getUser->rows()[0]->data()['uid'];
                        app('firebase.firestore')->database()->collection('users')->document($request->uid[$i])->collection('groups')->document($uid)->set([
                            'id' => $uid,
                            'name' => $request->group_name,
                            'profile_picture' => $group->profile_picture,
                        ]);
                    }
                } else {
                    $members[$i]['email'] = $isSuperadmin->rows()[0]->data()['email'];
                    $members[$i]['isAdmin'] = true;
                    $members[$i]['name'] = $isSuperadmin->rows()[0]->data()['name'];
                    $members[$i]['uid'] = $isSuperadmin->rows()[0]->data()['uid'];
                    app('firebase.firestore')->database()->collection('users')->document($isSuperadmin->rows()[0]->data()['uid'])->collection('groups')->document($uid)->set([
                        'id' => $uid,
                        'name' => $request->group_name,
                        'profile_picture' => $group->profile_picture,
                    ]);
                }
            }

            $group = app('firebase.firestore')->database()->collection('groups')->document($uid)->set([
                'id' => $uid,
                'members' => $members,
                'name' => $request->group_name,
                'group_description' => $request->group_description ?? null,
                'profile_picture' =>  $group->profile_picture,
            ]);

            return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Group created successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $th->getMessage()], 401);
        }
    }

    // media api for firebase
    public function media(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,  'error' => $errors], 401);
        }

        // get image from firebase group chat by group id
        $group = app('firebase.firestore')->database()->collection('groups')->document($request->group_id)->collection('chats')
        ->where('type', '=', 'img')
        ->documents();
        // return $group->rows()[0]->data();
        $message = [];
        foreach ($group->rows() as $key => $value) {
            $message[$key]['img'] = $value->data()['message'];
            $message[$key]['time'] = date('M', strtotime($value->data()['time']));
        }
        
        if ($group->rows() == null) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'Group not found'], 401);
        } else {
            return response()->json(['status' => true, 'statusCode' => 200, 'data' => $message], 200);
        }
    }

    // media image download by link
    public function mediaImageDownload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,  'error' => $errors], 401);
        }

        // download file by url
        $path = $request->url;
        // randowm alphabetic name
        $name = Str::random(30);
        Storage::disk('local')->put($name, file_get_contents($path));
  
        $path = Storage::path($name);
  
        return response()->download($path);

    }

    public function groupList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,  'error' => $errors], 401);
        }

        // get group list by uid
        $group = app('firebase.firestore')->database()->collection('users')->document($request->uid)->collection('groups')->documents();
        $groupList = [];
        foreach ($group->rows() as $key => $value) {
            // group admin name
            $groupAdmin = app('firebase.firestore')->database()->collection('groups')->document($value->data()['id']);
            foreach ($groupAdmin->snapshot()->data()['members'] as $sd => $admin) {
               if ($admin['isAdmin'] == true) {
                     $groupList[$key]['admin'] = $admin['name'];
                     break;
               }
            }
            $groupList[$key]['id'] = $value->data()['id'];
            $groupList[$key]['name'] = $value->data()['name'];
            $groupList[$key]['profile_picture'] = $value->data()['profile_picture'];
        }
        
        return response()->json(['status' => true, 'statusCode' => 200, 'data' => $groupList], 200);
    }
    
}
