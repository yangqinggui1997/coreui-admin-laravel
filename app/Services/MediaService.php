<?php
namespace App\Services;

use Google\Cloud\Firestore\FieldValue;

class MediaService extends FirebaseServices
{
    protected $_mediaCollection;
    
    public function __construct()
    {
        $this->_mediaCollection = self::mediaCollection();
    }

    public static function createMedia($data)
    {
        if(!$data) return false;
        
        $userCollection = self::userCollection();

        $media = (new self)->_mediaCollection->newDocument();

        $data["user_id"] = $userCollection->document($data["user_id"]);
        $data["createdAt"] = FieldValue::serverTimestamp();
        $data["updatedAt"] = FieldValue::serverTimestamp();

        $media->set($data);

        return $media->id();
    }
}