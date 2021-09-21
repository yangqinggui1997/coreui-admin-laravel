<?php

namespace Database\Seeders;

use App\Helpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert level 1 category
        $data = [];
        $dataLevel2 = [];
        $index = 1;
        for ($i = 0; $i < 20 ; $i++) { 
            $name = Str::random();
            $dataLevel2[] = $data[] = [
                "id" => $index++,
                "name" => $name,
                "parent_id" => 0,
                "seo_name" => Helpers::CONVERT_vn($name),
                "thumbnail" => "bds-nha-dat.jpeg",
                "order" => $i
            ];
        } 

        //Insert level 2 category
        
        for ($i = 0; $i < count($data) ; $i++) { 
            for($j = 0; $j < 20; ++$j)
            {
                $name = Str::random();
                $dataLevel2[] = [
                    "id" => $index++,
                    "name" => $name,
                    "parent_id" => $data[$i]["id"],
                    "seo_name" => Helpers::CONVERT_vn($name),
                    "thumbnail" => Str::random(),
                    "order" => $j
                ];
            }
        }

        DB::table("category")->insert($dataLevel2);
    }
}
