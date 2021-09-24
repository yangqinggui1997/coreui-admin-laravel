<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot as LINELINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\Text\EmojiBuilder;
use LINE\LINEBot\MessageBuilder\Text\EmojiTextBuilder;

class LineBotController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function messages()
    {
        $httpClient = new CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $bot = new LINELINEBot($httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);

        $signature = $this->request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature))
            Log::error("Bad request");

        // Check request with signature and parse request
        try 
        {
            $events = $bot->parseEventRequest(file_get_contents("php://input"), str_replace("\\", "", $signature));
            Log::info("EVENTS: ".$events);
        } 
        catch (InvalidSignatureException $e) 
        {
            Log::error($e->getMessage());
        } 
        catch (InvalidEventRequestException $e) 
        {
            Log::error($e->getMessage());
        }

        foreach ($events as $event) {
            Log::info("Message instance: ");
            Log::info(print_r($event, true));
            switch(true)
            {
                case $event instanceof TextMessage:
                    $replyText = new EmojiTextBuilder("$ LINE emoji $", new EmojiBuilder(0, "5ac1bfd5040ab15980c9b435", "001"), new EmojiBuilder(13, "5ac1bfd5040ab15980c9b435", "002"));
                    Log::info("Reply text: ");
                    $resp = $bot->replyText($event->getReplyToken(), $replyText, "hi there");
                    break;
                default: 
                    Log::error('Non text message has come');
                    break;
            }
            Log::info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
        }
    }
}
