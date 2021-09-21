<?php
namespace App\Services;

use Google\Cloud\Firestore\FieldValue;

class GroupUserService extends FirebaseServices
{
    protected $_groupUserCollection;

    public function __construct()
    {
        $this->_groupUserCollection = self::groupUserCollection();
    }

    public static function fbIndex()
    {
        $groups = (new self)->_groupUserCollection->documents();

        $data = [];
        foreach($groups as $group)
        {
            if($group->exists())
            {
                $_data = $group->data();
                $data[] = (object)[
                    "id" => $group->id(),
                    "name" => $_data["name"],
                    "numberOfMembers" => count($_data["users"]),
                    "type" => $_data["type"]
                ];
            }
        }

        return $data;
    }

    public static function fbGetById($id)
    {
        $group = (new self)->_groupUserCollection->document($id);

        $snapshot = $group->snapshot();

        if($snapshot->exists())
        {
            return (object)["id" => $group->id(), "name" => $snapshot->data()["name"], "type" => $snapshot->data()["type"]];
        }

        return NULL;
    }

    public static function fbGetGroupUserNotAdmin()
    {
        $groups = (new self)->_groupUserCollection->where("type", "in", [1,2]);
        $documents = $groups->documents();

        $data = [];
        foreach($documents as $doc)
            $data[] = (object)[
                "id" => $doc->id(),
                "name" => $doc["name"]
            ];

        return $data;
    }

    public static function fbCreate($data)
    {
        if(!$data) return false;

        $group = (new self)->_groupUserCollection->newDocument();
        $data["createdAt"] = FieldValue::serverTimestamp();
        $data["updatedAt"] = FieldValue::serverTimestamp();
        
        $group->set($data);

        return $group->id();
    }

    public static function fbUpdate($id, $data)
    {
        if(!$data) return false;

        $group = (new self)->_groupUserCollection->document($id);
        
        $snapshot = $group->snapshot();

        if($snapshot->exists())
        {
            $data["updatedAt"] = FieldValue::serverTimestamp();
        
            $dataUpdate = [];
            foreach($data as $key => $value)
                $dataUpdate[] = [
                    "path" => $key,
                    "value" => $value
                ];
            self::transaction(function($transaction) use($group, $dataUpdate){
                $transaction->update($group, $dataUpdate);
            });

            return true;
        }
        
        return false;
    }

    public static function fbDelete($id)
    {
        $category = (new self)->_groupUserCollection->document($id);

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
            return true;
        }
        return false;
    }

    public static function getAdminGroup()
    {
        $group = (new self)->_groupUserCollection->where("type", "=", 0)->limit(1);
        $docGroup = !$group->documents()->isEmpty() ? $group->documents()->rows()[0]->reference() : NULL;
        return $docGroup;
    }

    public static function getUserGroup()
    {
        $group = (new self)->_groupUserCollection->where("type", "!=", 0);
        $docGroup = !$group->documents()->isEmpty() ? $group->documents()->rows() : [];
        
        $docRefs = [];

        foreach($docGroup as $docQuerySnapshot)
            $docRefs[] = $docQuerySnapshot->reference();

        return $docRefs;
    }
}