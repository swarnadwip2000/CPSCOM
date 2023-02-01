<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $data = app('firebase.firestore')->database()->collection('users')->documents();  
            print_r('Total records: '.$data->size());  
            // dd($data->rows());
            foreach($data->rows() as $item) {  
                if($item->exists()){  
                    // dd($item->data()['status']);
                    print_r('ID = '.$item->data()['uid']);  
                    print_r('Name = '.$item->data()['name']);
                }  
            }  
            
        return view('admin.users.list');
    }
}
