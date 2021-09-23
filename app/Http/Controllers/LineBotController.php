<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot as LINELINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\SignatureValidator;

class LineBotController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function messages()
    {
        // get request body and line signature header
        $body 	   = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

        // log body and signature
        file_put_contents('php://stderr', 'Body: '.$body);

        // is LINE_SIGNATURE exists in request header?
        if (empty($signature))
            return response()->json(["error" => "Signature not set"], 400);

        // is this request comes from LINE?
        if(!$_ENV['PASS_SIGNATURE'] && ! SignatureValidator::validateSignature($body, $_ENV['LINE_BOT_CHANNEL_SECRET'], $signature))
            return response()->json(["error" => "Invalid signature"], 400);

        // init bot
        $httpClient = new CurlHTTPClient($_ENV['LINE_BOT_CHANNEL_ACCESS_TOKEN']);
        $bot = new LINELINEBot($httpClient, ['channelSecret' => $_ENV['LINE_BOT_CHANNEL_SECRET']]);
        $data = json_decode($body, true);
        
        foreach ($data['events'] as $event)
        {
            $userMessage = $event['message']['text'];
            if(strtolower($userMessage) == 'halo')
            {
                $message = "Halo juga";
                $textMessageBuilder = new TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            
            }
        }
    }
}
