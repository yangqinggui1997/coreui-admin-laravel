<?php

namespace App\Http\Controllers;

use App\Services\LineService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;

class LineBotController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function messages()
    {
        $signature = $this->request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature))
            Log::error("Bad request");

        // Check request with signature and parse request
        try 
        {
            $events = LineService::getEvents(file_get_contents("php://input"), str_replace("\\", "", $signature));
            Log::info("EVENTS: ".print_r($events, true));

            foreach ($events as $event) 
            {
    
                Log::info("Message instance: ");
                Log::info(print_r($event, true));
    
                switch(true)
                {
                    case $event instanceof TextMessage:
                        !UserService::checkUserExistsByLineId($event->getUserId()) && LineService::replyRequireRegister($event->getReplyToken());
                        break;
                    default: 
                        Log::error('None text message has come.');
                        break;
                }
            }
        } 
        catch (InvalidSignatureException $e) 
        {
            Log::error($e->getMessage());
        } 
        catch (InvalidEventRequestException $e) 
        {
            Log::error($e->getMessage());
        }
    }
}
