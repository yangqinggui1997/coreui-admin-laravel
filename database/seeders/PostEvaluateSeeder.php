<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostEvaluateSeeder extends Seeder
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
        $data = [];

        foreach($posts as $post)
            foreach($users as $user)
            {
                if($post->user_id == $user->id || rand(0, 1)) continue;
                $data[] = [
                    "post_id" => $post->id,
                    "user_evaluate_id" => $user->id,
                    "content" => Str::random(),
                    "amount_of_start" => rand(1, 5)
                ];
            }
        
        foreach(array_chunk($data, 1000) as $d)
            DB::table("post_evaluates")->insert($d);
    }
}
