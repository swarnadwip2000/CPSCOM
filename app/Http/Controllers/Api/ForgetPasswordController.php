<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerificationMail;

class ForgetPasswordController extends Controller
{
    public $successStatus = 200;
    public function submitForgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
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

        $data = app('firebase.firestore')->database()->collection('users');
        $query = $data->where('email', '=', $request->email);
        $documents = $query->documents();
        $count = count($documents->rows());
        if ($count > 0) {
            $verify =  DB::table('password_resets')->where([
                ['email', $request->all()['email']]
            ]);
        
            if ($verify->exists()) {
                $verify->delete();
            }
            $otp = random_int(1000, 9999);
            $password_reset = DB::table('password_resets')->insert([
                'email' => $request->all()['email'],
                'token' =>  $otp,
                'created_at' => Carbon::now()
            ]);
            Mail::to($request->email)->send(new OtpVerificationMail($otp));
            return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'OTP sent successfully.'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Email not found!', 'status' => false], 401);
            
        }
    }


    public function submitOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'otp' => 'required',
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

        $count = DB::table('password_resets')->where([
            ['email', $request->all()['email']],
            ['token', $request->all()['otp']]
        ])->count();

       
        if ($count > 0) {
            $verify =  PasswordReset::where([
                ['email', $request->all()['email']],
                ['token', $request->all()['otp']]
            ])->first();
            
            $newTime =  date('h:i A', strtotime( $verify->created_at->addHour()));
            $currentTime = date('h:i A');
            if ($newTime < $currentTime) {
                return response()->json(['message' => 'OTP expired!', 'status' => false], 401);
            }
            
            return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'OTP verified successfully.'], $this->successStatus);
        } else {
            return response()->json(['message' => 'OTP not matched!', 'status' => false], 401);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
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

            $data = app('firebase.firestore')->database()->collection('users');
            $query = $data->where('email', '=', $request->email);
            $documents = $query->documents();
            $count = count($documents->rows());
            if ($count > 0) {
                $documents->rows()[0]->reference()->update([
                    ['path' => 'password', 'value' => Hash::make($request->password)]
                ]);
                return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Password reset successfully.'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Email not found!', 'status' => false], 401);
            }
    }

}
