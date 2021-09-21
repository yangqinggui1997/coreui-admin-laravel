<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaveOrSharePostListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = DB::table("post")->select(["id", "user_id"])->get();

        $users = DB::select("SELECT id FROM users WHERE !FIND_IN_SET('admin', menuroles)");

        $usersId = [];
        foreach($users as $user)
            $usersId[] = $user->id;

        $data = [];
        foreach($posts as $post)
        {
            foreach($usersId as $id)
            {
                if($post->user_id == $id) continue;
                if(rand(0, 1))
                    $data[] = [
                        "user_id" => $id,
                        "post_id" => $post->id,
                        "type" => rand(0, 1)
                    ];
            }
        }

        foreach (array_chunk($data, 1000) as $t)
            DB::table("save_or_share_post_list")->insert($t);
    }
}
