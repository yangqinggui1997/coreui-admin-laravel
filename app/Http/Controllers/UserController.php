<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Jobs\SendPushLineMessageJob;
use App\Services\GroupUserService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $you = $this->request->auth; 
        $users = UserService::getListUser();
        return view('dashboard.admin.user.index', compact(['users', 'you']));
    }

    public function show($id)
    {
        $user = UserService::getUserById($id);
        if($user->status)
        {
            $user = $user->infors;
            return view('dashboard.admin.user.userShow', compact('user'));
        }
        return back()->withErrors($user->errorMessage);
    }

    public function signinPage()
    {
        return view('auth.login');
    }

    public function signupPage()
    {
        return view('auth.register');
    }

    public function signin()
    {
        $email = $this->request->input('email');
        $password = $this->request->input("password");
        $user = UserService::signInWithEmailAndPassword($email, $password);
        if($user->status)
        {
            Session::put("firebaseIdToken", $user->tokens->id_token);
            return redirect()->route('home');
        }
        else return back()->withErrors($user->errorMessage."|")->withInput();
    }

    public function signup()
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'required|string',
            'email' => 'email',
            'password' => 'required|string|max:32|min:6',
            'phone' => 'required|numeric',
            'carPlate' => 'required|string',
            'lineId' => 'required|string'
        ]);

        if($validator->fails())
            return back()->withErrors($validator->getMessageBag())->withInput();
        $user = UserService::createUser([
            "displayName" => $this->request->input("name"),
            "email" => !array_key_exists('email', $this->request->all()) ? $this->request->input("lineId").".".$this->request->input("phone")."@gmail.com" : $this->request->input("email"),
            "password" => $this->request->input("password"),
            "phoneNumber" => $this->request->input("phone")
        ],
        [
            "carPlate" => $this->request->input("carPlate"),
            "lineId" => $this->request->input("lineId"),
            "chatbotVerified" => false,
            "emailVerifiedAt" => NULL,
            "role" => "user",
            "groupId" => NULL,
            "status" => true
        ]
        );
        if($user->status)
        {
            $user = UserService::signInWithEmailAndPassword($user->infors->email, $this->request->input("password"));
            if($user->status)
            {
                Session::put("firebaseIdToken", $user->tokens->id_token);
                return redirect()->route('home');
            }
            else return back()->withErrors($user->errorMessage)->withInput();
        }
        return back()->withErrors($user->errorMessage)->withInput();
    }

    public function emailResetPasswordPage()
    {
        return view('auth.passwords.email');
    }

    public function resetPasswordPage()
    {
        return view('auth.passwords.reset');
    }

    public function emailResetPassword()
    {

    }

    public function signout()
    {
        Session::forget('firebaseIdToken');
        return redirect()->route('signinPage');
    }

    public function create()
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string'
        ]);

        if($validator->fails())
            return back()->withErrors($validator->getMessageBag())->withInput();
        
        $password = Str::random();

        $data = [
            "displayName" => $this->request->input("name"),
            "email" => $this->request->input("email"),
            "password" => $password
        ];

        if(array_key_exists('phone', $this->request->all()) && $this->request->input("phone"))
            $data["phoneNumber"] = $this->request->input("phone");
        $user = UserService::createUser($data,
        [
            "carPlate" => NULL,
            "lineId" => NULL,
            "chatbotVerified" => NULL,
            "emailVerifiedAt" => NULL,
            "role" => "admin",
            "status" => $this->request->input("status") ? true : false
        ]
        );
        if($user->status)
        {
            Queue::push((new SendMailJob($this->request->input("email"), ["email" => $this->request->input("email"), "password" => $password], 'create-account')));
            return back()->withInput()->with(["success" => true]);
        }
        return back()->withErrors($user->errorMessage)->withInput();
    }

    public function edit($id)
    {
        $ip = $this->request->ip();
        $geo = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $country = property_exists($geo, "country") ? $geo->country : NULL;
        if('create' === strtolower($id))
            return view("dashboard.admin.user.form", compact("country"));

        $user = UserService::getUserById($id);
        if(!$user->status)
            return back()->withErrors($user->errorMessage)->withInput();
        $user = $user->infors;
        
        $groupUser = [];
        
        $groups = GroupUserService::getUserGroup();
        foreach($groups as $docRef)
            $groupUser[] = (object)[
                "id" => $docRef->id(),
                "name" => $docRef->snapshot()->data()["name"] 
            ];
        return view("dashboard.admin.user.form", compact("user", "country", "groupUser"));
    }
    
    public function update()
    {
        $validator = Validator::make($this->request->all(), [
            "id" => 'required|string',
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        if($validator->fails())
            return back()->withErrors($validator->getMessageBag())->withInput();
        
        $data = [
            "displayName" => $this->request->input("name"),
            "email" => $this->request->input("email")
        ];

        $claims = [
            "status" => $this->request->input("status") ? true : false
        ];

        if(array_key_exists('phone', $this->request->all()) && $this->request->input("phone"))
            $data["phoneNumber"] = $this->request->input("phone");

        if(array_key_exists("groupUser", $this->request->all()))
            $claims["groupUser"] = $this->request->input("groupUser");
            
        $result = UserService::updateUser($this->request->input("id"), $data, $claims);
        if(!$result->status)
            return back()->withErrors($this->request->input("phone"))->withInput();
        return $this->index();
    }

    public function delete()
    {
        $validator = Validator::make($this->request->all(), [
            "id" => 'required|string'
        ]);

        if($validator->fails())
            return back()->withErrors($validator->getMessageBag())->withInput();

        $result = UserService::deleteUser($this->request->input('id'));
        if(!$result->status) return back()->withErrors($result->errorMessage);
        Queue::push((new SendMailJob($result->data["email"], null, 'remove-account')));
        return $this->index();
    }

    public function register()
    {
        return view("auth.register")->with(array_key_exists("lineId", $this->request->all()) ? ["lineId" => $this->request->input("lineId")] : []);
    }

    public function proccessRegitser()
    {
        $allRequestData = $this->request->all();
        $validator = Validator::make($allRequestData, [
            "name" => "required",
            "phone" => "required",
            "email" => "nullable|email",
            "carPlate" => "required",
            "lineId" => "required"
        ]);

        if($validator->fails())
            return back()->withInput()->withErrors($validator->getMessageBag());

        $password = Str::random();

        $data = [
            "dislayName" => $this->request->input("name"),
            "phoneNumber" => $this->request->input("phone"),
            "password" => $password,
            "email" => array_key_exists('email', $allRequestData) ? $this->request->input("email") : $this->request->input("lineId").".".$this->request->input("phone")."@gmail.com"
        ];

        $claims = [
            "carPlate" => $this->request->input("carPlate"),
            "lineId" => $this->request->input("lineId"),
            "chatbotVerified" => false,
            "emailVerifiedAt" => NULL,
            "role" => "user",
            "groupId" => NULL,
            "status" => true
        ];

        $result = UserService::createUser($data, $claims);

        if($result->status)
        {
            array_key_exists('email', $allRequestData) && Queue::push((new SendMailJob(array_key_exists('email', $allRequestData) ? $this->request->input("email") : env("MAIL_FROM_ADDRESS"), ["email" => $this->request->input("email"), "password" => $password], 'create-account')));

            Queue::push((new SendPushLineMessageJob($this->request->input("lineId"))));

            return back()->withInput()->with(["success" => "Congratulations!, your account created successfully."]);
        } 
        return back()->withErrors($result->errorMessage)->withInput();
    }
}
