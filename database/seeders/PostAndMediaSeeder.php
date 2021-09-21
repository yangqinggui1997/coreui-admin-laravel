<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class PostAndMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = DB::table("post")->select(["id", "user_id"])->get();

        $data = [];
        foreach($posts as $post)
        {
            if(rand(0,3)) continue;
            $data[] = [
                "post_id" => $post->id,
                "media_id" => rand(4,5)
            ];
        }

        DB::table("post_and_media")->insert($data);
    }
}
