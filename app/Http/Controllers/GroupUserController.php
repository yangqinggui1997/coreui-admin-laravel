<?php

namespace App\Http\Controllers;

use App\Services\GroupUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupUserController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function fbIndex()
    {
        $groups = GroupUserService::fbIndex();
        return view("dashboard.admin.group-user.index", compact('groups'));
    }

    public function fbGetById($id)
    {
        $group = GroupUserService::fbGetById($id);
        
        if('create' === strtolower($id))
            return view("dashboard.admin.group-user.form");
        
        return view("dashboard.admin.group-user.form", compact("group"));
    }

    public function fbCreate()
    {
        $validator = Validator::make($this->request->all(), [
            "name" => "required|string"
        ]);

        if($validator->fails())
        {
            $error = $validator->getMessageBag();
            return back()->withErrors($error)->withInput();
        }

        $data = [
            "name" => $this->request->input("name"),
            "type" => 2
        ];

        GroupUserService::fbCreate($data);

        return $this->fbIndex();
    }

    public function fbUpdate()
    {
        $validator = Validator::make($this->request->all(), [
            "id" => "required|string",
            'name' => 'required|string'
        ]);

        if($validator->fails())
            return back()->withErrors($validator->getMessageBag())->withInput();
            
        $data = [
            "name" => $this->request->input("name")
        ];

        GroupUserService::fbUpdate($this->request->input('id'), $data);

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

        GroupUserService::fbDelete($this->request->input('id'));
        return $this->fbIndex();
    }
}
