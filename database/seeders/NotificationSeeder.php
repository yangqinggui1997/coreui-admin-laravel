<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
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
            $receivers = DB::table("users_receiver_notification")->where("sender_id", $post->user_id)->select("receiver_id")->get();

            foreach($receivers as $r)
                $data[] = [
                    "sender_id" => $post->user_id,
                    "receiver_id" => $r->receiver_id,
                    "post_id" => $post->id,
                    "title" => Str::random(),
                    "content" => Str::random(50)
                ];
        }

        $admins = DB::select("SELECT id FROM users WHERE FIND_IN_SET('admin', menuroles)");
        $users = DB::select("SELECT id FROM users WHERE !FIND_IN_SET('admin', menuroles)");

        foreach($admins as $admin)
            foreach($users as $user)
            {
                if(rand(0,3)) continue;
                $data[] = [
                    "sender_id" => $admin->id,
                    "receiver_id" => $user->id,
                    "post_id" => NULL,
                    "title" => Str::random(),
                    "content" => Str::random(50)
                ];
            }
        
        DB::table("notifications")->insert($data);
    }
}
