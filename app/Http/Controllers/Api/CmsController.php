<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms;
use Illuminate\Support\Facades\Validator;

class CmsController extends Controller
{
    public $successStatus = 200;
    
    public function getStarted(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_panel' => 'required|in:user,admin',
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
            $count = Cms::where('is_panel', $request->is_panel)->count();
            if ($count > 0) {
                $cms = Cms::where('is_panel', $request->is_panel)->select('id','title','description','image')->first();
                $data['cms'] = $cms;
                return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Cms found successfully.', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['message' => 'No detail found!', 'status' => false], 401);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'something went wrong' , 'status' => false], 401);
        }

    }
}
