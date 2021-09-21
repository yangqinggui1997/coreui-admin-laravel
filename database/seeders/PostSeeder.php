<?php

namespace Database\Seeders;

use App\Helpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = DB::table("category")->where("parent_id", "<>", 0)->get();

        $users = DB::select("SELECT id FROM users WHERE !FIND_IN_SET('admin', menuroles)");

        $usersId = [];
        foreach($users as $user)
            $usersId[] = $user->id;

        $data = [];
        foreach($category as $cat)
        {
            for($i = 0; $i < 20; $i ++)
            {
                $title = Str::random();
                $content = Str::random(50);
                $data[] = [
                    "category_id" => $cat->id,
                    "user_id" => $usersId[array_rand($usersId)],
                    "title" => $title,
                    "content" => $content,
                    "seo_title" => Helpers::CONVERT_vn($title),
                    "seo_content" => Helpers::CONVERT_vn($content),
                    "thumbnail" => "img2.jpeg"
                ];
            }
        }
        DB::table("post")->insert($data);
    }
}
