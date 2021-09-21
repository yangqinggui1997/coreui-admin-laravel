<?php 

namespace App\Services;

// use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseServices;
use Google\Cloud\Firestore\FieldValue;

class CategoryService extends FirebaseServices
{
    protected $_categoryCollecttion;

    public function __construct()
    {
        $this->_categoryCollecttion = self::categoryCollection();
    }
    
    public static function getAll()
    {
        $categories = DB::table('category')->select(["id", "parent_id", "name", "order", "thumbnail"])->paginate(10);

        foreach($categories as $cat)
        {
            $cat->parent = ($cate = DB::table("category")->where("id", $cat->parent_id)->select("name")->first()) ? $cate->name : "Unknow";
        }
        return [
            "data" => $categories,
            "pagination" => $categories->links()
        ];
    }

    private static function getAllChild($parent, &$output, &$route)
    {
        $route[] = $parent->name;
        $output[] = [
            "id" => $parent->id,
            "name" => $parent->name,
            "route" => count($route) > 1 ? implode(" > ", $route) : $route[0],
            "order" => $parent->order
        ];
        $childs = DB::table("category")->where("parent_id", $parent->id)->select(["id", "name", "order"])->get();
        if(count($childs))
        {
            foreach($childs as $ch)
                self::getAllChild($ch, $output, $route);
        }
        else
            return;
    }

    public static function getById($id)
    {
        return DB::table("category")->find($id, ["id", "parent_id", "name", "thumbnail"]);
    }

    public static function getAllCat()
    {
        return DB::table("category")->select(["id", "name", "parent_id", "thumbnail"])->get();
    }

    public static function update($id, $data)
    {
        $category = DB::table("category")->first();
        DB::transaction(function() use($id, $data){
            DB::table("category")->where("id", $id)->update($data);
        }, 2);

        if($category->thumbnail)
        {
            $filePath = parse_url($category->thumbnail, PHP_URL_PATH);
            if(file_exists(public_path().$filePath))
                unlink(public_path().$filePath);
        }

        return self::getById($id);
    }

    public static function delete($id)
    {
        $category = DB::table('category')->find($id, ["id", "thumbnail"]);
        if(!$category) return false;

        $result = true;
        DB::transaction(function() use(&$result, $id) {
            $result = DB::table("category")->where("id", $id)->delete();
        }, 2);

        $filePath = parse_url($category->thumbnail, PHP_URL_PATH);
        if($result && file_exists(public_path().$filePath))
            unlink(public_path().$filePath);
        return true;
    }

// Firebase database functions
    public static function fbIndex()
    {
        $categories = (new self)->_categoryCollecttion->documents();

        $data = [];
        foreach($categories as $cat)
        {
            if($cat->exists())
            {
                $_cat = $cat->data();
                $_cat = (object)$_cat;
                $_data = [
                    "id" => $cat->id(),
                    "name" => $_cat->name,
                    "order" => $_cat->order,
                    "thumbnail" =>$_cat->thumbnail,
                    "parent" => "Unknow"
                ];
                if($_cat->parent_id)
                {
                    $parent = $_cat->parent_id->snapshot();
                    $parent = $parent->exists() ? $parent->data() : [];
                    $_data["parent"] = array_key_exists("name", $parent) ? $parent["name"] : "Unknow";
                }
                $data[] = (object)$_data;
            }
        }
        return $data;
    }

    public static function fbGetRootCategory()
    {
        $query = (new self)->_categoryCollecttion->where('parent_id', '=', NULL);
        $categories = $query->documents();
        $data = [];
        foreach($categories as $cat)
        {
            if($cat->exists())
            {
                $_cat = $cat->data();
                $_cat = (object)$_cat;
                $_data = [
                    "id" => $cat->id(),
                    "name" => $_cat->name
                ];
                $data[] = (object)$_data;
            }
        }
        return $data;
    }
    
    public static function fbGetById($id)
    {
        $category = (new self)->_categoryCollecttion->document($id);

        $snapshot = $category->snapshot();
        if($snapshot->exists())
        {
            $data = $snapshot->data();
            $parent = $data["parent_id"] ? $data["parent_id"]->snapshot() : NULL;
            return (object)[
                "id" => $snapshot->id(), 
                "parent_id" => $parent ? ($parent->exists() ?  $parent->id() : "") : "", 
                "parent_name" => $parent ? ($parent->exists() ?  $parent->data()["name"] : "") : "", 
                "name" => $data["name"], 
                "thumbnail" => $data["thumbnail"]
            ];
        }

        return NULL;
    }

    public static function fbGetByParent($parent)
    {
        $query = (new self)->_categoryCollecttion->where('parent_id', '=', (new self)->_categoryCollecttion->document($parent->id));
        $category = $query->documents();

        $data = [];
        foreach($category as $cate)
        {
            if($cate->exists())
            {
                $_cat = $cate->data();
                $_cat = (object)$_cat;
                $_data = [
                    "id" => $cate->id(),
                    "name" => $_cat->name
                ];
                $data[] = (object)$_data;
            }
        }

        return $data;
    }

    public static function fbCreate($data)
    {
        if(!$data) return false;
        $category = (new self)->_categoryCollecttion->newDocument();

        if(array_key_exists('parent_id', $data))
            $data["parent_id"] = $data["parent_id"] ? (new self)->_categoryCollecttion->document($data["parent_id"]) : NULL;
        $data["createdAt"] = FieldValue::serverTimestamp();
        $data["updatedAt"] = FieldValue::serverTimestamp();
        
        $category->set($data);

        return $category->id();
    }

    public static function fbUpdate($id, $data)
    {
        if(!$data) return false;
        $category = (new self)->_categoryCollecttion->document($id);

        $snapshot = $category->snapshot();
        if($snapshot->exists())
        {
            if(array_key_exists('parent_id', $data))
                $data["parent_id"] = $data["parent_id"] ? (new self)->_categoryCollecttion->document($data["parent_id"]) : NULL;

            $data['updatedAt'] = FieldValue::serverTimestamp();
            $dataUpdate = [];
            foreach($data as $key => $value)
                $dataUpdate[] = [
                    "path" => $key,
                    "value" => $value
                ];
            self::transaction(function($transaction) use($category, $dataUpdate){
                $transaction->update($category, $dataUpdate);
            });

            if(array_key_exists("thumbnail", $snapshot->data()) && ($filePath = parse_url($snapshot->data()["thumbnail"], PHP_URL_PATH)))
                if(file_exists(public_path().$filePath))
                    unlink(public_path().$filePath);

            return true;
        }

        return false;
    }

    public static function fbDelete($id)
    {
        $category = (new self)->_categoryCollecttion->document($id);

        $snapshot = $category->snapshot();

        if($snapshot->exists())
        {
            try
            {
                self::transaction(function($transaction) use($category){
                    $transaction->delete($category);
                });
            }
            catch(\Throwable $e)
            {
                return false;
            }

            if($snapshot->exists() && array_key_exists("thumbnail", $snapshot->data()) && $snapshot->data()["thumbnail"])
            {
                $filePath = parse_url($snapshot->data()["thumbnail"], PHP_URL_PATH);
                if(file_exists(public_path().$filePath))
                    unlink(public_path().$filePath);
            }
            return true;
        }
        return false;
    }
}