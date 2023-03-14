<?php

namespace App\Helpers;


class Helper
{
    public static function adminGroupName($uid)
    {
        $group = app('firebase.firestore')->database()->collection('users')->document($uid)->collection('groups')->documents();
        // foreach ($group as $key => $value) {
        //     $group = $value->data()['group'];
        // }
        return $group->rows();
    }
}
