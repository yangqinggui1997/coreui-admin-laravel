<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserReceiveNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = DB::select("SELECT id FROM users WHERE !FIND_IN_SET('admin', menuroles)");

        $usersId = [];
        foreach($users as $user)
            $usersId[] = $user->id;
        
        $data = [];
        foreach($usersId as $id)
            foreach($usersId as $_id)
            {
                if($id === $_id || rand(0, 10)) continue;
                $data[] = [
                    "sender_id" => $id,
                    "receiver_id" => $_id
                ];
            }
                
                    
        DB::table("users_receiver_notification")->insert($data);
    }
}
