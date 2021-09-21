<?php

namespace App\Http\Controllers;

use App\Services\MediaService;
use Illuminate\Http\Request;

class UploadCotroller extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->uploadDir = env('UPLOAD_DIR');
    }

    public function uploadCKeditor() 
    {
        if($this->request->hasFile('upload'))
        {
            $file = $this->request->file('upload');
            $oryginalName = $file->getClientOriginalName();
            $fileName = time()."_".uniqid()."_".$oryginalName;
            $filePath = $file->storeAs($this->request->auth->sub, $fileName, 'uploads');

            $data = [
                "user_id" => $this->request->auth->sub,
                "name" => $file->getClientOriginalName(),
                "file_name" => $fileName,
                "url" => $this->request->getSchemeAndHttpHost()."/".$this->uploadDir."/".$filePath,
                "mime_type" => $file->getClientMimeType(),
                "size" => $file->getSize()
            ];
            
            $id = MediaService::createMedia($data);

            return response()->json([
                "uploaded" => true,
                "url" => $this->request->getSchemeAndHttpHost()."/uploads/".$filePath
            ]);
        }
        return response()->json([
            "uploaded" => false,
            "error" => [
                "message" => "Something went wrong!"
            ]
        ]);
    }
}
