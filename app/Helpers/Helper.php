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


    public static function groupAdminName($groupId)
    {
        // get group admin name members array of group collection 
        $group = app('firebase.firestore')->database()->collection('groups')->document($groupId)->snapshot()->data();
        $members = $group['members'];
        // get the data which isAdmin is true in array
       $admin = array_filter($members, function ($member) {
            return $member['isAdmin'] == true;
        }) ;
        return $admin; 
    }

    // count total members of a group
    public static function totalMembers($groupId)
    {
        $group = app('firebase.firestore')->database()->collection('groups')->document($groupId)->snapshot()->data();
        $members = count($group['members']);
       if ($members > 0) {
              return $members;
         } else {
              return 0;
       }
    }
}
