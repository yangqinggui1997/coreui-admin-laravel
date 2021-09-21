<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = DB::table("post")->select("id")->get();

        $users = DB::select("SELECT id FROM users WHERE !FIND_IN_SET('admin', menuroles)");

        $usersId = [];
        foreach($users as $user)
            $usersId[] = $user->id;

        $data = [];
        $index = 0;
        foreach($posts as $post)
        {
            for($i = 0; $i < 2; ++$i)
            {
                $data[] = [
                    "parent_id" => rand(0, $index),
                    "post_id" => $post->id,
                    "user_id" => $usersId[array_rand($usersId)],
                    "content" => Str::random()
                ];
                $index++;
            }
        }
        DB::table("comment")->insert($data);
    }
}
