<?php
namespace App\Services;

use DateTime;
use Google\Cloud\Firestore\FieldPath;
use Google\Cloud\Firestore\FieldValue;

class UserService extends FirebaseServices
{
    protected $_userCollection;

    public function __construct()
    {
        $this->_userCollection = self::userCollection();
    }
    
    public static function getUserById($userId)
    {
        try 
        {
            $user = (new self)->_userCollection->document($userId);
            $snapshot = $user->snapshot();
            if($snapshot->exists())
            {
                $_user = self::auth()->getUser($userId);

                $userData = $snapshot->data();
                
                $groupUser = [];
                $groupUserCollection = self::groupUserCollection();
                $groupDocRefs = $groupUserCollection->documents();
                foreach($groupDocRefs as $docRef)
                {
                    $query = $groupUserCollection->where(FieldPath::documentId(), "=", $docRef->id())->where("users", "array-contains", $user);
                    !$query->documents()->isEmpty() && $groupUser[] = $docRef->id();
                }
                $userData["groupUser"] = $groupUser;
                $user = array_merge((array)$_user, $userData);
            }
            else
                $user = self::auth()->getUser($userId);

            return (object)["status" => true, "infors" => (object)$user];
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }

    public static function checkUserExistsByLineId($lineId)
    {
        $query = (new self)->_userCollection->where("lineId", "=", $lineId);
        $querySanpshot = $query->documents();

        if($querySanpshot->isEmpty()) return false;
        
        return true;
    }

    public static function getUserByEmail($email)
    {
        try 
        {
            return (object)["status" => true, "infors" => self::auth()->getUserByEmail($email)];
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }

    public static function getUserByPhoneNumber($phoneNumber)
    {
        try 
        {
            return (object)["status" => true, "infors" => self::auth()->getUserByPhoneNumber($phoneNumber)];
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }

    public static function getListUser()
    {
        try
        {
            $users = self::auth()->listUsers();

            $data = [];
            foreach ($users as $user) {
                if($user->metadata->lastLoginAt)
                    $user->lastLogin = DateTime::createFromImmutable($user->metadata->lastLoginAt)->format('h:i:s A d/m/Y');
                else
                    $user->lastLogin = "";
                $fsUser = self::fsGetUserById($user->uid);
                $data[] = (object) array_merge((array)$user, $fsUser);
            }
    
            return $data;
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorMessage" => $e->getMessage(), "errorCode" => $e->getCode()];
        }
    }

    public static function createUser($userMetaData = [
        'email' => 'user@example.com',
        'emailVerified' => false,
        'phoneNumber' => '+15555550100',
        'password' => '123456',
        'displayName' => 'John Doe',
        'photoUrl' => 'http://www.example.com/12345678/photo.png',
        'disabled' => false,
    ], $claims = [])
    {
        try
        {
            $user = self::auth()->createUser($userMetaData);
            $create = self::fsCreateUser($user->uid, $claims);
            if(!$create->status)
                throw new \Error($create->errorMessage, $create->errorCode);
            $user = self::getUserById($user->uid);
            return $user;
        }
        catch(\Exception $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }

    public static function updateUser($userId, $userMetaDataUpdate, $claims)
    {
        if(!$userMetaDataUpdate) return false;

        $user = (new self)->_userCollection->document($userId);

        $claims['updatedAt'] = FieldValue::serverTimestamp();

        $dataGroupUser = [];
        if(array_key_exists("groupUser", $claims))
        {
            $dataGroupUser = $claims["groupUser"];
            unset($claims["groupUser"]);
        }

        $dataUpdate = [];
        foreach($claims as $key => $value)
            $dataUpdate[] = [
                "path" => $key,
                "value" => $value
            ];

        try
        {
            self::auth()->updateUser($userId, $userMetaDataUpdate);
            $snapshot = $user->snapshot();
            if($snapshot->data()["role"] !== "user")
                self::transaction(function($transaction) use($user, $dataUpdate){
                    $transaction->update($user, $dataUpdate);
                });
            else
            {
                $groupUserDocuments = self::groupUserCollection()->documents();
                
                self::transaction(function($transaction) use($user, $dataUpdate, $dataGroupUser, $groupUserDocuments){

                    $transaction->update($user, $dataUpdate);
                    
                    foreach($groupUserDocuments as $groupDocSnapshot)
                    {
                        in_array($groupDocSnapshot->id(), $dataGroupUser) 
                        && (
                            $transaction->update($groupDocSnapshot->reference(), [
                                [
                                    "path" => "users",
                                    "value" => FieldValue::arrayUnion([$user])
                                ]
                            ]) 
                            || 1
                        ) 
                        || $transaction->update($groupDocSnapshot->reference(), [
                            [
                                "path" => "users",
                                "value" => FieldValue::arrayRemove([$user])
                            ]
                        ]);
                    }
                });
            }
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }

        return (object)["status" => true];
    }

    public static function changePassWord($userId, $newPassword)
    {
        $updatedUser = self::auth()->changeUserPassword($userId, $newPassword);
        return $updatedUser;
    }

    public static function changeEmail($userId, $newEmail)
    {
        $updatedUser = self::auth()->changeUserEmail($userId, $newEmail);
        return $updatedUser;
    }

    public static function disableUser($userId)
    {
        $updatedUser = self::auth()->disableUser($userId);
        return $updatedUser;
    }

    public static function enableUser($userId)
    {
        $updatedUser = self::auth()->enableUser($userId);
        return $updatedUser;
    }

    public static function deleteUser($userId)
    {
        try 
        {
            $user = (new self)->_userCollection->document($userId);

            $snapshot = $user->snapshot();
            $adminGroupDoc = GroupUserService::getAdminGroup();

            if($snapshot->exists())
                self::transaction(function($transaction) use($user, $adminGroupDoc){
                    $transaction->delete($user);
                    $transaction->update($adminGroupDoc, [
                        [
                            "path" => "users",
                            "value" => FieldValue::arrayRemove([$user])
                        ]
                    ]);
                });

            $user = self::auth()->getUser($userId);
            $data = [
                "email" => $user->email
            ];
            self::auth()->deleteUser($userId);

            return (object)["status" => true, "data" => $data];
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }

    public static function deleteMultiUser($userIds)
    {
        $forceDeleteEnabledUsers = true; // default: false

        $result = self::auth()->deleteUsers($userIds, $forceDeleteEnabledUsers);

        return (object)["countSuccess" => $result->successCount(), "countFail" => $result->failureCount(), "errorMessages" => $result->rawErrors()];
    }

    public static function signInWithEmailAndPassword($email, $password)
    {
        try
        {
            $signInResult = self::auth()->signInWithEmailAndPassword($email, $password);
            return (object)["status" => true, "infors" => (object)$signInResult->data(), "tokens" => (object)$signInResult->asTokenResponse()];
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }

    public static function signInWithRefreshToken($refreshToken)
    {
        try
        {
            $signInResult = self::auth()->signInWithRefreshToken($refreshToken);
            return (object)["status" => true, "infors" => (object)$signInResult->data(), "tokens" => (object)$signInResult->asTokenResponse()];
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorCode" => $e->getCode(), "errorMessage" => $e->getMessage()];
        }
    }

    protected static function fsCreateUser($uid, $metadata = [])
    {
        if(!$metadata) return (object)["status" => false, "errorMessage" => "data is null.", "errorCode" => "data-null"];

        try
        {
            $user = (new self)->_userCollection->document($uid);
            $metadata["createdAt"] = FieldValue::serverTimestamp();
            $metadata["updatedAt"] = FieldValue::serverTimestamp();

            if($metadata["role"] === "admin")
            {
                $adminGroupDoc = GroupUserService::getAdminGroup();

                self::transaction(function($transaction) use($user, $metadata, $adminGroupDoc) {
                    $transaction->create($user, $metadata);
                    $transaction->update($adminGroupDoc, [
                        [
                            "path" => "users",
                            "value" => FieldValue::arrayUnion([$user])
                        ]
                    ]);
                });
            }
            else
                $user->set($metadata);

            return (object)["status" => true];
        }
        catch(\Throwable $e)
        {
            return (object)["status" => false, "errorMessage" => $e->getMessage(), "errorCode" => $e->getCode()];
        }
    }

    protected static function fsGetUserById($uid)
    {
        $user = (new self)->_userCollection->document($uid);
        $userSnapshot = $user->snapshot();
        if($userSnapshot->exists())
            return $userSnapshot->data();
        return [];
    }
}