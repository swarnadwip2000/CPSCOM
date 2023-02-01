<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['ADMIN'];
        foreach ($roles as $role) {
            $arole = new Role;
            $arole->name = $role;
            $arole->save();
        }
    }
}
