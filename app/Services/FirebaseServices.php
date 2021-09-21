<?php 

namespace App\Services;

use Error;
use Firebase\Auth\Token\Exception\InvalidToken;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Transaction;
use Kreait\Firebase\Auth\CreateSessionCookie\FailedToCreateSessionCookie;

class FirebaseServices 
{
    protected $database;
    protected $firestore;
    protected $auth;

    public function __construct()
    {
        $this->database = app('firebase.firestore');
        $this->firestore = app('firebase.firestore')->database();
        $this->auth = app('firebase.auth');
    }

    public static function firestore()
    {
        return (new self)->database;
    }

    public static function auth()
    {
        return (new self)->auth;
    }

    public static function database()
    {
        return (new self)->firestore;
    }

    public static function categoryCollection()
    {
        return (new self)->firestore->collection('category');
    }

    public static function postCollection()
    {
        return (new self)->firestore->collection('post');
    }

    public static function userCollection()
    {
        return (new self)->firestore->collection('user');
    }

    public static function mediaCollection()
    {
        return (new self)->firestore->collection('media');
    }

    public static function groupUserCollection()
    {
        return (new self)->firestore->collection('group_user');
    }

    public static function transaction(callable $closure)
    {
        $firestore = new FirestoreClient([
            'keyFile' => json_decode(file_get_contents(app()->basePath('config')."/coreui-admin-laravel-firebase-adminsdk-xk265-2478e28a18.json"), true)
        ]);
        
        $firestore->runTransaction(function (Transaction $transaction) use ($closure) {
            $closure($transaction);
        });
    }

    public static function generateToken($userId, $additionalClaims = [])
    {
        $customToken = (new self)->auth->createCustomToken($userId, $additionalClaims);

        return $customToken->toString();
    }

    public static function verifiedToken($token)
    {
        try 
        {
            $verifiedIdToken = (new self)->auth->verifyIdToken($token);
            $user = $verifiedIdToken->claims()->all();
            $additionData = UserService::getUserById($user["sub"]);
            if($additionData->status)
                $user = array_merge($user, (array)$additionData->infors);
            else
                throw new Error($additionData->errorMessage);

            return (object)["status" => true, "user" => $user];
        } 
        catch (InvalidToken $e) 
        {
            return (object)["status" => false, "errorMessage" => $e->getMessage()];
        } 
        catch (\InvalidArgumentException $e) 
        {
            return (object)["status" => false, "errorMessage" => $e->getMessage()];
        }
        catch(\Exception $e)
        {
            return (object)["status" => false, "errorMessage" => $e->getMessage()];
        }
    }

    public static function createSessionCookie($idToken, $expire)
    {
        try 
        {
            $sessionCookieString = (new self)->auth->createSessionCookie($idToken, $expire);
            return (object)["status" => true, "cookie" => $sessionCookieString];
        } 
        catch(FailedToCreateSessionCookie $e) 
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }
}