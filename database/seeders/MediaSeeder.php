<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = DB::select("SELECT id FROM users WHERE !FIND_IN_SET('admin', menuroles)");

        $data = [];
        foreach($users as $user)
            $data[] = [
                "user_id" => $user->id,
                "type" => "image",
                "link" => "http://coreui-admin.yang/uploads/users/".$user->id."/img".$user->id.".jpeg"
            ];
        DB::table("media")->insert($data);
    }
}
