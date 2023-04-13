<?php

namespace App\Helpers;


class Helper
{
    public static function adminGroupName($uid)
    {
        $group = app('firebase.firestore')->database()->collection('users')->document($uid)->collection('groups')->documents();
        return $group->rows();
    }

    public static function userName($uid)
    {
        $user = app('firebase.firestore')->database()->collection('users')->document($uid)->snapshot()->data();
        return $user['name'];
    }
}
