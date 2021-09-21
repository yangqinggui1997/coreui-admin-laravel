<?php

namespace App;

use App\Services\CategoryService;

class Helpers {
    public static function CONVERT_vn($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/( )/", '-', $str);
        $str = preg_replace("/%/", 'Phan-Tram', $str);
        $str = preg_replace("@[^A-Za-z0-9./\-_]+@i", "", $str);
        $str = preg_replace("/(--)/", '-', $str);
        $str = preg_replace("/:/", '-', $str);
        $str = str_replace("/", '-', $str);
        return trim($str, "-");
    }

    public static function buildTreeCategory(&$data, $items)
    {
        foreach($items as $c)
        {
            $_data = [
                "title" => $c->name,
                "dataAttrs" => [
                    [
                        "title" => "id",
                        "data" => $c->id
                    ]
                ]
            ];

            $_child = [];

            $child = CategoryService::fbGetByParent($c);

            self::buildTreeCategory($_child, $child);

            if($_child)
                $_data['data'] = $_child;

            $data[] = (object)$_data;
        }
    }

    public static function handlerUpload($file, $outputDir)
    {
        $oryginalName = $file->getClientOriginalName();
        $fileName = time()."_".uniqid()."_".$oryginalName;
        $filePath = $file->storeAs($outputDir, $fileName, 'uploads');
        return $filePath;
    }
}