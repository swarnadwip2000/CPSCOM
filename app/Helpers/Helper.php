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


    public static function groupAdminName($members)
    {
        // get the data which isAdmin is true in array
       $admin = array_filter($members, function ($member) {
            return $member['isAdmin'] == true;
        }) ;
        // only get the name of admin
        $adminName = array_map(function ($member) {
            return $member['name'];
        }, $admin);
        // return the name of admin
        return implode(', ', $adminName);
    }
    

    // count total members of a group
    public static function totalMembers($groupId)
    {
        $group = app('firebase.firestore')->database()->collection('groups')->document($groupId)->snapshot()->data();
       if ($group['members'] != null) {
           return count($group['members']);
       } else {
           return 0;
       }
    }
}
