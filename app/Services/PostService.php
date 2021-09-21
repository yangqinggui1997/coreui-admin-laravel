<?php 

namespace App\Services;

// use App\Models\Post;
use Google\Cloud\Firestore\FieldValue;
use Illuminate\Support\Facades\DB;

class PostService extends FirebaseServices
{ 
    protected $_postCollection;

    public function __construct()
    {
        $this->_postCollection = self::postCollection();
    }
    
    public static function getAll()
    {
        $posts = DB::table('post')->select(["id", "user_id", "category_id", "title", "link", "thumbnail", "author", "amount_of_display", "amount_of_view", "created_at", "updated_at", "page_link_name"])->paginate(10);

        foreach($posts as $post)
        {
            $post->postUserName = ($user = DB::selectOne("SELECT (CASE WHEN (FIND_IN_SET('admin', menuroles)) THEN 'Admin' ELSE name END) AS name FROM users WHERE id = ?", [$post->user_id])) ? $user->name : "Unknow";
            $post->category = ($cate = DB::table("category")->where("id", $post->category_id)->select("name")->first()) ? $cate->name : "Unknow";
            $post->created_at = date('h:i:s A d/m/Y', strtotime($post->created_at));
            $post->updated_at = date('h:i:s A d/m/Y', strtotime($post->updated_at));
            $post->author = $post->author ? $post->author.($post->page_link_name ? ' ('. $post->page_link_name.')' : '') : $post->postUserName;
        }
        return [
            "data" => $posts,
            "pagination" => $posts->links()
        ];
    }

    public static function getById($id)
    {
        return DB::table("post")->find($id);
    }

    public static function update($id, $data)
    {
        DB::transaction(function() use($id, $data){
            DB::table("post")->where("id", $id)->update($data);
        }, 2);
        return self::getById($id);
    }

    public static function delete($id)
    {
        $post = DB::table('post')->find($id, ["id", "thumbnail"]);
        if(!$post) return false;

        $result = true;
        DB::transaction(function() use(&$result, $id) {
            $result = DB::table("post")->where("id", $id)->delete();
        }, 2);

        $filePath = parse_url($post->thumbnail, PHP_URL_PATH);
        if($result && file_exists(public_path().$filePath))
            unlink(public_path().$filePath);
        return true;
    }

    public static function fbIndex()
    {
        $posts = (new self)->_postCollection->documents();

        $data = [];
        foreach($posts as $post)
        {
            if($post->exists())
            {
                $_post = $post->data();

                $postUserName = "Unknow";
                $user = $_post['user_id']->snapshot();
                if($user->exists())
                {
                    $userSnapshot = UserService::getUserById($user->id());
                    $postUserName = $userSnapshot->status ? $userSnapshot->infors->displayName : "Unknow";
                }

                $category = $_post["category_id"]->snapshot();

                $_data = [
                    "id" => $post->id(),
                    "category" => $category->exists() ? $category->data()["name"] : "Unknow",
                    "post_user_name" => $postUserName,
                    "title" => $_post["title"], 
                    "link" => $_post["link"], 
                    "thumbnail" => $_post["thumbnail"], 
                    "author" => $_post["author"] ? $_post["author"].($_post["page_link_name"] ? ' ('. $_post["page_link_name"].')' : '') : $postUserName, 
                    "amount_of_display" => $_post["amount_of_display"], 
                    "amount_of_view" => $_post["amount_of_view"], 
                    "status" => $_post["status"],
                    "created_at" => $_post["createdAt"]->get()->format('h:i:s A d/m/Y'), 
                    "updated_at" => $_post["updatedAt"]->get()->format('h:i:s A d/m/Y'), 
                    "page_link_name" => $_post["page_link_name"]
                ];
                
                $data[] = (object)$_data;
            }
        }
        return $data;
    }

    public static function fbGetById($id)
    {
        $post = (new self)->_postCollection->document($id);

        $snapshot = $post->snapshot();
        if($snapshot->exists())
        {
            $data = $snapshot->data();
            $categorySnapshot = $data["category_id"]->snapshot();

            foreach($data["groupUser"] as $k => $docSnapshot)
                $docSnapshot->snapshot()->exists() && $data["groupUser"][$k] = $docSnapshot->id();

            return (object)[
                "id" => $snapshot->id(), 
                "category_id" => $categorySnapshot ? ($categorySnapshot->exists() ?  $categorySnapshot->id() : NULL) : NULL,
                "category_name" => $categorySnapshot ? ($categorySnapshot->exists() ?  $categorySnapshot->data()["name"] : NULL) : NULL,
                "title" => $data["title"], 
                "link" => $data["link"],
                "author" => $data["author"],
                "status" => $data["status"],
                "page_link_name" => $data["page_link_name"],
                "thumbnail" => $data["thumbnail"],
                "content" => $data["content"],
                "groupUser" => $data["groupUser"]
            ];
        }

        return NULL;
    }

    public static function fbCreate($data)
    {
        if(!$data) return false;

        $categoyCollection = self::categoryCollection();
        $userCollection = self::userCollection();

        $post = (new self)->_postCollection->newDocument();

        $data["category_id"] = $categoyCollection->document($data["category_id"]);
        $data["user_id"] = $userCollection->document($data["user_id"]);

        $data["createdAt"] = FieldValue::serverTimestamp();
        $data["updatedAt"] = FieldValue::serverTimestamp();   

        $groupUserCollection = self::groupUserCollection();

        $data["groupUser"] 
        && (
            (function() use(&$data, $groupUserCollection){foreach($data["groupUser"] as $k => $group)
                $data["groupUser"][$k] = $groupUserCollection->document($group);
            })()
            || 1
        )
        || $data["groupUser"] = [];

        self::transaction(function($transaction) use($post, $data) {
            $transaction->create($post, $data);
        });

        return $post->id();
    }

    public static function fbUpdate($id, $data)
    {
        if(!$data) return false;

        $categoyCollection = self::categoryCollection();

        $post = (new self)->_postCollection->document($id);

        $snapshot = $post->snapshot();
        $postData = $snapshot->exists() ? $snapshot->data() : NULL;

        if($postData)
        {
            $categorySnapshot = $postData["category_id"]->snapshot();
            $categoryId = $categorySnapshot->exists() ? $categorySnapshot->id() : NULL;
            if($data["category_id"] && $data["category_id"] === $categoryId)
                unset($data["category_id"]);
            else
                $data["category_id"] = $categoyCollection->document($data["category_id"]);
        }

        $groupUserCollection = self::groupUserCollection();
        $data["groupUser"] 
        && (
            (function() use(&$data, $groupUserCollection){foreach($data["groupUser"] as $k => $group)
                $data["groupUser"][$k] = $groupUserCollection->document($group);
            })()
            || 1
        )
        || $data["groupUser"] = [];

        $data['updatedAt'] = FieldValue::serverTimestamp();
        $dataUpdate = [];
        foreach($data as $key => $value)
            $dataUpdate[] = [
                "path" => $key,
                "value" => $value
            ];

        self::transaction(function($transaction) use($post, $dataUpdate){
            $transaction->update($post, $dataUpdate);
        });

        if($snapshot->exists() && array_key_exists("thumbnail", $data))
        {
            if(array_key_exists("thumbnail", $snapshot->data()) && ($filePath = parse_url($snapshot->data()["thumbnail"], PHP_URL_PATH)))
                if(file_exists(public_path().$filePath))
                    unlink(public_path().$filePath);
        }

        return true;
    }

    public static function fbDelete($id)
    {
        $post = (new self)->_postCollection->document($id);

        $snapshot = $post->snapshot();

        if($snapshot->exists())
        {
            try
            {
                self::transaction(function($transaction) use($post){
                    $transaction->delete($post);
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