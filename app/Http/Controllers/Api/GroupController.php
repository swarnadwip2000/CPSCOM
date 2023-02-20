<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Group;
use App\Models\User;
use Kreait\Firebase\Factory;

class GroupController extends Controller
{
    public $successStatus = 200;
    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)
                    ->withServiceAccount(__DIR__.'/firebase_credential.json')
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
            $count = Group::where('group_id',$request->group_id)->count();
            if ($count > 0) {
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
    
                return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Image uploaded successfully'], 200);
            } else {
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
    
                return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Image uploaded successfully'], 200);
            }
        } else {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'Group not found'], 401);
        }
       
    }
}
