<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cms;

class addGetStartedPageCms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $panel = ['user','admin'];
        foreach ($panel as $key => $value) {
            $cms = new Cms();
            $cms->title = 'Lorem ipsum dolor sit amet consectetur Nibh quisque amet';
            $cms->description = 'Lorem ipsum dolor sit amet consectetur Nibh quisque amet';
            $cms->image = 'cms/get_started_1.png';
            $cms->is_panel = $value;
            $cms->save();
        }
    }
}
