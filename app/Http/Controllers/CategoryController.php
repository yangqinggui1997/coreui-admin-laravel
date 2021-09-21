<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $category = CategoryService::getAll();
        return view("dashboard.admin.category.index", compact('category'));
    }

    public static function getById($id)
    {
        $category = CategoryService::getById($id);
        $categories = CategoryService::getAllCat();
        return view("dashboard.admin.category.form", compact("category", "categories"));
    }

    public function update()
    {
        $validator = Validator::make($this->request->all(), [
            'name'    => 'required|string',
            'categoryParent' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            $error = $validator->getMessageBag();
            return back()->withErrors($error);
        }

        $filePath = NULL;
        if($this->request->hasFile('thumbnail')){
            $file = $this->request->file('thumbnail');
            $oryginalName = $file->getClientOriginalName();
            $fileName = time()."_".uniqid()."_".$oryginalName;
            $filePath = $file->storeAs(Auth::id(), $fileName, 'uploads');
            
        }
        $data = [
            "name" => $this->request->input("name"),
            "parent_id" => $this->request->input("categoryParent")
        ];

        if($filePath)
            $data['thumbnail'] = $this->request->getSchemeAndHttpHost()."/uploads/".$filePath;

        CategoryService::update($this->request->input("id"), $data);
        $category = CategoryService::getAllCat();
        return view("dashboard.admin.category.index", compact("category"));
    }

    public function delete()
    {
        $result = CategoryService::delete($this->request->input('id'));
        $category = CategoryService::getAll();
        return view("dashboard.admin.category.index", compact('category'));
    }

    public function fbIndex()
    {
        $category = CategoryService::fbIndex();
        return view("dashboard.admin.category.index", compact('category'));
    }

    public function fbGetById($id)
    {
        $categories = [];
        $_categories = CategoryService::fbGetRootCategory();
        Helpers::buildTreeCategory($categories, $_categories);
        
        if('create' === strtolower($id))
            return view("dashboard.admin.category.form", compact("categories"));

        $category = CategoryService::fbGetById($id);

        return view("dashboard.admin.category.form", compact("category", "categories"));
    }

    public function fbCreate()
    {
        $validator = Validator::make($this->request->all(), [
            "name" => "required|string",
            "categoryParent" => "nullable|string",
            "thumbnail" => "file|image"
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
            "name" => $this->request->input("name"),
            "display" => true,
            "order" => 0
        ];

        if(array_key_exists('categoryParent', $this->request->all()))
            $data['parent_id'] = $this->request->input("categoryParent");

        $data['thumbnail'] = $filePath ? $this->request->getSchemeAndHttpHost()."/uploads/".$filePath : NULL;

        CategoryService::fbCreate($data);

        return $this->fbIndex();
    }
    
    public function fbUpdate()
    {
        $validator = Validator::make($this->request->all(), [
            "id" => 'required|string',
            "name"    => 'required|string',
            "categoryParent" => "nullable|string",
            "thumbnail" => "file|image"
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
            "name" => $this->request->input("name")
        ];
        if(array_key_exists('categoryParent', $this->request->all()) && $this->request->input("categoryParent") !== $this->request->input("id"))
            $data['parent_id'] = $this->request->input("categoryParent");

        if($filePath)
            $data['thumbnail'] = $this->request->getSchemeAndHttpHost()."/uploads/".$filePath;

        CategoryService::fbUpdate($this->request->input("id"), $data);

        return $this->fbIndex();
    }

    public function fbDelete()
    {
        $validator = Validator::make($this->request->all(), [
            "id" => 'required|string'
        ]);

        if($validator->fails())
        {
            $error = $validator->getMessageBag();
            return back()->withErrors($error);
        }

        CategoryService::fbDelete($this->request->input('id'));
        return $this->fbIndex();
    }
}
