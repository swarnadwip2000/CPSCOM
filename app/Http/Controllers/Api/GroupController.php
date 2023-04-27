<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Str;
 
/**
 * @group Group APIs
 *
 * APIs for Group
 */

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

    /**
     *  Group Image Upload API
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam image file required Image
     * @bodyParam group_id string required Group Id
     * @response {
     * "status": true,
     * "statusCode": 200,
     * "message": "Image uploaded successfully"
     * }
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The image field is required.",
     * "The group id field is required."
     * ]
     * }
     * }
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The image must be an image.",
     * "The image must be a file of type: jpeg, png, jpg, gif, svg."
     * ]
     * }
     * }
     * @response 401 {
     * "message": "Group not found!",
     * "status": false
     * }
     */
    public function imageUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,webp,jpg,gif,svg|max:2048',
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
                            ['path' => 'profile_picture', 'value' => $image_path ?? null]
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
                            ['path' => 'profile_picture', 'value' => $image_path ?? null]
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

    /**
     *  Group Member API
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam id string required Group Id
     * @response {
     * "status": true,
     *   "statusCode": 200,
     *   "data": [
     *       {
     *           "uid": "0CBwrNCGBacPQbVl2BJo6qg1VPZ2",
     *           "name": "Ronald Urrutia IT",
     *           "email": "rurrutia@cpsmh.org",
     *           "isAdmin": false,
     *           "profile_picture": "user/qlbw7dUk022X4vVELvZAHRMSOPiY7woMSHLClbxU.png"
     *       }
     *   ]
     * }
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The id field is required."
     * ]
     * }
     * }
     */
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
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $th->getMessage()], 401);
        }
    }
    /**
     * Create Group API
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam group_name string required Group Name
     * @bodyParam uid array required User Id
     * @response {
     * "status": true,
     *  "statusCode": 200,
     * "message": "Group created successfully"
     * }
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The group_name field is required.",
     * "The uid.0 field is required."
     * ]
     * }
     * }
     */
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
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
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
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
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
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                }
            }

            app('firebase.firestore')->database()->collection('groups')->document($uid)->set([
                'id' => $uid,
                'members' => $members,
                'name' => $request->group_name,
                'group_description' => $request->group_description ?? null,
                'profile_picture' =>  $group->profile_picture,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Group created successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $th->getMessage()], 401);
        }
    }

    /**
     *  Media Image
     * 
     * @bodyParam group_id string required The id of the group.
     * @response {
     *  "status": true,
     *    "statusCode": 200,
     *    "data": [
     *        {
     *            "img": "https://firebasestorage.googleapis.com/v0/b/cps-com-c90aa.appspot.com/o/cpscom_admin_images%2F23442a60-cf95-11ed-8210-fb21b722f56f.jpg?alt=media&token=80ad2885-3797-4adc-96de-eb83172e6a52",
     *            "time": "Mar"
     *        }
     *    ]
     * }
     *  @response 401 {
     *  "status": false,
     *  "statusCode": 401,
     *  "error": {
     *    "message": [
     *     "The group id field is required."
     *   ]
     *  }
     * }
     * 
     */

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

    /**
     *  Media Image Download
     *  @bodyParam url string required The url of the image.
     * @response {
     *  "status": true,
     *   "statusCode": 200,
     * 
     * }
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The url field is required."
     * ]
     * }
     */
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

    /**
     * Group List
     *  @bodyParam uid string required The id of the user.
     * @response {
     * "status": true,
     *   "statusCode": 200,
     *   "data": {
     *       "0": {
     *           "admin": "Tony nelson",
     *           "id": "AJolDjZVXfywCFtjgTVEBSxWaYPudIUHVMhK",
     *           "name": "Nurses at Bristol",
     *           "profile_picture": "group/1qQtvEY71EM76wDJsoNXXCK6tWfGWJtlJaPspRaD.png"
     *       },
     *       "2": {
     *           "admin": "CRClinic",
     *           "id": "fRQDiLq0sMbAmc9QMFmUkL07cAWhIaiW3TVX",
     *           "name": "New Group",
     *           "profile_picture": "group/wiFx4czB5JyNlzTm0nbW5Lhzf0dw2GaRsPe8ZQQZ.png"
     *       }
     *   }
     * } 
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The uid field is required."
     * ]
     * }
     * }
     * 
     */

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

        try {
            // $group = app('firebase.firestore')->database()->collection('users')->document($request->uid)->collection('groups')->documents();
            $group = app('firebase.firestore')->database()->collection('users')->document($request->uid)->collection('groups')->orderBy('created_at', 'asc')->documents();

            $groupList = [];
            foreach ($group->rows() as $key => $value) {
                // group admin name
                // return $value->data();
                $groupAdmin = app('firebase.firestore')->database()->collection('groups')->document($value->data()['id']);
                foreach ($groupAdmin->snapshot()->data()['members'] as $sd => $admin) {
                    if ($admin['isAdmin'] == true) {
                        $admins = app('firebase.firestore')->database()->collection('users')->document($admin['uid'])->snapshot()->data();
                        $groupList[$key]['admin'] = $admins['name'];
                        break;
                    }
                }
                $groupList[$key]['id'] = $value->data()['id'];
                $groupList[$key]['name'] = $value->data()['name'];
                $groupList[$key]['profile_picture'] = $value->data()['profile_picture'];
                $groupList[$key]['created_at'] = $value->data()['created_at'];
            }

            if ($request->has('search') && $request->search != null) {
                // array keyword search
                $search = explode(' ', $request->search);
                $groupList = array_filter($groupList, function ($item) use ($search) {
                    foreach ($search as $key => $value) {
                        if (stripos($item['name'], $value) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
            }


            return response()->json(['status' => true, 'statusCode' => 200, 'data' => $groupList], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $th->getMessage()], 401);
        }
    }
}
