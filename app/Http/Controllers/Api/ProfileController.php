<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;

class ProfileController extends Controller
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

    public function getProfileImage(Request $request)
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
            $count = User::where('uid', $request->uid)->count();
            if ($count > 0) {
                $user = User::where('uid', $request->uid)->select('id', 'profile_picture')->first();
                $data['user'] = $user;
                return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Profile image found successfully.', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['message' => 'No detail found!', 'status' => false], 401);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'something went wrong', 'status' => false], 401);
        }
    }

    public function uploadProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'name' => 'required',
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

            $count = User::where('uid', $request->uid)->count();
            if ($count > 0) {
                $user = User::where('uid', $request->uid)->first();
                if ($request->hasFile('profile_picture')) {
                    $file = $request->file('profile_picture');
                    $filename = date('YmdHi') . $file->getClientOriginalName();
                    $image_path = $request->file('profile_picture')->store('user', 'public');
                    $user->profile_picture = $image_path;
                }
                $user->update();
                $data = app('firebase.firestore')->database()->collection('users')->documents();
                foreach ($data as $key => $value) {
                    if ($value->data()['uid'] == $request->uid) {
                        $user = app('firebase.firestore')->database()->collection('users')->document($value->id())
                            ->update([
                                ['path' => 'profile_picture', 'value' => $image_path],
                                ['path' => 'name', 'value' => $request->name],
                            ]);
                    }
                }
            } else {
                $data = $this->auth->getUser($request->uid);
                $user = new User;
                $user->uid =  $request->uid;
                $user->name =  $data->displayName;
                $user->email =  $data->email;
                $user->password =  bcrypt('12345678');
                if ($request->hasFile('profile_picture')) {
                    $file = $request->file('profile_picture');
                    $filename = date('YmdHi') . $file->getClientOriginalName();
                    $image_path = $request->file('profile_picture')->store('user', 'public');
                    $user->profile_picture = $image_path;
                }
                $user->save();
                $data = app('firebase.firestore')->database()->collection('users')->documents();
                foreach ($data as $key => $value) {
                    if ($value->data()['uid'] == $request->uid) {
                        $user = app('firebase.firestore')->database()->collection('users')->document($value->id())
                            ->update([
                                ['path' => 'profile_picture', 'value' => $image_path],
                                ['path' => 'name', 'value' => $request->name],
                            ]);
                    }
                }
            }

            $properties = [
                'displayName' => $request->name,
            ];
            $updatedUser = $this->auth->updateUser($request->uid, $properties);

            return response()->json(['status' => true, 'statusCode' => 200, 'data' => $user, 'message' => 'Profile picture updated successfully'], $this->successStatus);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'something went wrong'], 401);
        }
    }
}
