<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Services\CategoryService;
use App\Services\GroupUserService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $posts = PostService::getAll();
        return view("dashboard.admin.post.index", compact('posts'));
    }

    public function getById($id)
    {
        $post = DB::table('post')->find($id);
        $categories = CategoryService::getAllCat();
        return view("dashboard.admin.post.form", compact("post", "categories"));
    }

    public function update()
    {
        $validator = Validator::make($this->request->all(), [
            'title'    => 'required|string',
            'content' => 'required',
            'postCategory' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            $error = $validator->getMessageBag();
            $post = DB::table('post')->find($this->request->input("id"));
            $categories = CategoryService::getAllCat();
            return view("dashboard.admin.post.form", compact("post", "categories", "error"));
        }

        $filePath = NULL;
        if($this->request->hasFile('thumbnail')){
            $file = $this->request->file('thumbnail');
            $oryginalName = $file->getClientOriginalName();
            $fileName = time()."_".uniqid()."_".$oryginalName;
            $filePath = $file->storeAs(Auth::id(), $fileName, 'uploads');
            
        }
        
        $data = [
            "title" => $this->request->input("title"),
            "content" => $this->request->input("content"),
            "link" => $this->request->input("link"),
            "author" => $this->request->input("author"),
            "page_link_name" => $this->request->input("pageLinkName"),
            "category_id" => $this->request->input("postCategory")
        ];

        if($filePath)
            $data["thumbnail"] = $this->request->getSchemeAndHttpHost()."/uploads/".$filePath;

        PostService::update($this->request->input("id"), $data);
        $posts = PostService::getAll();
        return view("dashboard.admin.post.index", compact('posts'));
    }

    public function delete()
    {
        $result = PostService::delete($this->request->input('id'));
        $posts = PostService::getAll();
        return view("dashboard.admin.post.index", compact('posts'));
    }

    public function fbIndex()
    {
        $posts = PostService::fbIndex();
        return view("dashboard.admin.post.index", compact('posts'));
    }

    public function fbGetById($id)
    {
        $post = PostService::fbGetById($id);

        $categories = [];
        $_categories = CategoryService::fbGetRootCategory();
        Helpers::buildTreeCategory($categories, $_categories);
        
        $groupUser = GroupUserService::fbGetGroupUserNotAdmin();
        if('create' === strtolower($id))
            return view("dashboard.admin.post.form", compact("categories", "groupUser"));
        
        return view("dashboard.admin.post.form", compact("post", "categories", "groupUser"));
    }

    public function fbCreate()
    {
        $validator = Validator::make($this->request->all(), [
            "title" => "required|string",
            'link' => 'nullable|url',
            'author' => 'nullable|string',
            'pageLinkName' => 'nullable|string',
            'content' => 'required|string',
            'categoryParent' => 'required|string',
            'thumbnail' => 'required|file|image',
            'status' => 'required|string'
        ]);

        if($validator->fails())
        {
            $error = $validator->getMessageBag();
            return back()->withErrors($error)->withInput();
        }

        $filePath = NULL;
        if($this->request->hasFile('thumbnail')){
            $file = $this->request->file('thumbnail');
            $filePath = Helpers::handlerUpload($file, $this->request->auth->sub);
        }
        $data = [
            "title" => $this->request->input("title"),
            "category_id" => $this->request->input("categoryParent"),
            "user_id" => $this->request->auth->sub,
            "content" => $this->request->input('content'),
            "seo_title" => Helpers::CONVERT_vn($this->request->input("title")),
            "seo_content" => Helpers::CONVERT_vn($this->request->input('content')),
            "link" => $this->request->input("link"),
            "author" => $this->request->input('author'),
            "page_link_name" => $this->request->input('pageLinkName'),
            "status" => $this->request->input("status"),
            "amount_of_display" => 0,
            "amount_of_view" => 0,
            "groupUser" => $this->request->input("groupUser")
        ];

        $data['thumbnail'] = $filePath ? $this->request->getSchemeAndHttpHost()."/uploads/".$filePath : NULL;

        PostService::fbCreate($data);

        return $this->fbIndex();
    }

    public function fbUpdate()
    {
        $validator = Validator::make($this->request->all(), [
            "id" => 'required|string',
            "title" => "string",
            'link' => 'nullable|url',
            'author' => 'nullable|string',
            'pageLinkName' => 'nullable|string',
            'content' => 'string',
            'categoryParent' => 'string',
            'thumbnail' => 'file|image',
            'status' => 'nullable|string'
        ]);

        if($validator->fails())
        {
            $error = $validator->getMessageBag();
            return back()->withErrors($error)->withInput();
        }

        $filePath = NULL;
        if($this->request->hasFile('thumbnail')){
            $file = $this->request->file('thumbnail');
            $filePath = Helpers::handlerUpload($file, $this->request->auth->sub);
        }
        $data = [
            "title" => $this->request->input("title"),
            "category_id" => $this->request->input("categoryParent"),
            "content" => $this->request->input('content'),
            "seo_title" => Helpers::CONVERT_vn($this->request->input("title")),
            "seo_content" => Helpers::CONVERT_vn($this->request->input('content')),
            "link" => $this->request->input("link"),
            "author" => $this->request->input('author'),
            "page_link_name" => $this->request->input('pageLinkName'),
            "status" => $this->request->input("status"),
            "groupUser" => $this->request->input("groupUser")
        ];

        if($filePath)
            $data['thumbnail'] = $this->request->getSchemeAndHttpHost()."/uploads/".$filePath;

        PostService::fbUpdate($this->request->input('id'), $data);

        return $this->fbIndex();
    }

    public function fbDelete()
    {
        $validator = Validator::make($this->request->all(), [
            "id" => 'required|string'
        ]);

        if($validator->fails())
            return back()->withErrors($validator->getMessageBag());

        PostService::fbDelete($this->request->input('id'));
        return $this->fbIndex();
    }
}
