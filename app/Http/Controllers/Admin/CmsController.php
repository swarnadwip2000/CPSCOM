<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms;

class CmsController extends Controller
{
    public function adminGetStarted()
    {
        $getStarted = Cms::where('is_panel','admin')->first();
        return view('admin.cms.sub-admin.get-started')->with(compact('getStarted'));
    }

    public function userGetStarted()
    {
        $getStarted = Cms::where('is_panel','user')->first();
        return view('admin.cms.user.get-started')->with(compact('getStarted'));
    }

    public function userGetStartedUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $getStarted = Cms::where('is_panel', $request->is_panel)->first();
        if($request->is_panel == 'admin'){
            $getStarted->title = $request->title;
            $getStarted->description = $request->description;
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'image|mimes:jpg,png,jpeg,gif,svg',
                ]);
                
                $file= $request->file('image');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('image')->store('cms', 'public');
                $getStarted->image = $image_path;
            }
            $getStarted->save();
            return redirect()->route('cms.sub-admin.get-started')->with('message', 'Get Started CMS Updated Successfully');
        } else {
            $getStarted->title = $request->title;
            $getStarted->description = $request->description;
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'image|mimes:jpg,png,jpeg,gif,svg',
                ]);
                
                $file= $request->file('image');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $image_path = $request->file('image')->store('cms', 'public');
                $getStarted->image = $image_path;
            }
            $getStarted->save();
            return redirect()->route('cms.user.get-started')->with('message', 'Get Started CMS Updated Successfully');
        }
    }
}
