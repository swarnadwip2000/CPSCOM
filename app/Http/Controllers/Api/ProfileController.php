<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;

/**
 * @group Profile APIs
 *
 * APIs for Profile
 */
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

    /**
     * Get Profile API
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam uid string required uid
     * @response {
     * "status": true,
     * "statusCode": 200,
     * "message": "Profile found successfully.",
     * "data": {
     * "user": {
     * "id": 1,
     * "name": "John Doe",
     * "email": "johh@yopmail.com",
     * "phone": "1234567890",
     *  "profile_picture": "https://cpscom-acb3c.firebaseio.com/user/2021-05-12-1620813781.jpg"
     * }
     * }
     * }
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The uid field is required."
     * ]
     *  }
     * }
     * @response 401 {
     * "message": "No detail found!",
     * "status": false
     * }
     */

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

    /**
     * Update Profile API
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @bodyParam uid string required uid
     * @bodyParam name string required User Name
     * @bodyParam profile_picture file required User Profile Picture
     * @response {
     * "status": true,
     * "statusCode": 200,
     * "data": {
     *     "id": 80,
     *     "uid": "UbMI7oRh1lQp3AO8Y0zBCPqiNNi1",
     *     "profile_picture": "user/GXQaUw5vXxNTXQ4YJ2qJNlJ2O9naz8KDHTJNRyvr.png",
     *     "name": "John Doe",
     *     "email": "john@yopmail.com",
     *     "status": 1,
     *     "created_at": "2023-03-15T06:15:27.000000Z",
     *     "updated_at": "2023-04-25T06:57:45.000000Z"
     * },
     * "message": "Profile picture updated successfully"
     * }
     * @response 401 {
     * "status": false,
     * "statusCode": 401,
     * "error": {
     * "message": [
     * "The uid field is required.",
     * "The name field is required."
     * ]
     * }
     * }
     * @response 401 {
     *  "message": "No detail found!",
     * "status": false
     * }
     */

    public function uploadProfile(Request $request)
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

            // get the uid valid or not in user collection
            $getValidUser = app('firebase.firestore')->database()->collection('users')->document($request->uid)->snapshot()->data();
            if ($getValidUser == null) {
                return response()->json(['message' => 'No detail found!', 'status' => false], 401);
            } else {
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
                }
                app('firebase.firestore')->database()->collection('users')->document($request->uid)
                    ->update([
                        ['path' => 'profile_picture', 'value' => $image_path ?? null],
                    ]);
                return response()->json(['status' => true, 'statusCode' => 200, 'data' => $user, 'message' => 'Profile picture updated successfully'], $this->successStatus);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $e->getMessage()], 401);
        }
    }
}
