<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Kreait\Firebase\Factory;
class AssignAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     protected $auth;

    public function __construct()
    {
        $factory = (new Factory)
                    ->withServiceAccount(__DIR__.'/firebase_credential.json')
                    ->withDataBaseUri('https://cpscom-acb3c.firebaseio.com');
        $this->auth = $factory->createAuth();
    }

    public function run()
    {
        // add super admin as a firebase authenticated users
        $name = 'Admin Admin "Derick"';
        $email = 'main@yopmail.com';
        $password = '12345678';
        $userProperties = [
            'email' => $email,
            'password' => '12345678',
            'displayName' => $name,
         ];
         $createdUser = $this->auth->createUser($userProperties);

        $data = app('firebase.firestore')->database()->collection('users')->document($createdUser->uid);
        $data->set([
            'email'=>$email,
            'status'=>'Unavalible',
            'name'=>$name,
            'uid'=>$createdUser->uid,
            'isAdmin'=>true,
            'isSuperAdmin'=>true,
        ]);
        $admin = new User;
        $admin->uid = $createdUser->uid;
        $admin->name = 'Admin Admin Derick';
        $admin->email = 'main@yopmail.com';
        $admin->password = bcrypt(12345678);
        $admin->save();
        $admin->assignRole('ADMIN');
    }
}
