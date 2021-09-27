<?php

namespace App\Services;

use LINE\LINEBot;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Text\EmojiBuilder;
use LINE\LINEBot\MessageBuilder\Text\EmojiTextBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class LineService
{
	public $httpClient;
	public $bot;

	// Declare construct function
	public function __construct()
	{
		$this->httpClient = new CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
		$this->bot = new LINEBot($this->httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);
	}

	public static function bot()
	{
		return (new self)->bot;
	}

	public static function httpClient()
	{
		return (new self)->httpClient;
	}

	public static function getEvents($bodyRequest, $signature)
	{
		return (new self)->bot->parseEventRequest($bodyRequest, $signature);
	}

	public static function pushMessageCreateAccountSuccess($to)
	{
		$getHTTPStatus = [];
		$getRawBody = [];

		$response = (new self)->bot->pushMessage($to, new TextMessageBuilder(new EmojiTextBuilder("Congratulations! $, your account creaeted successfully, $.", new EmojiBuilder(17, "5ac2213e040ab15980c9b447", "035"), new EmojiBuilder(56, "5ac22a8c031a6752fb806d66", "092"))));

		$getHTTPStatus[] = $response->getHTTPStatus();
		$getRawBody[] = $response->getRawBody();

		$response = (new self)->bot->pushMessage($to, new TextMessageBuilder("Thanks you for choosing our service, Please choice service that we can serve you: "));

		$getHTTPStatus[] = $response->getHTTPStatus();
		$getRawBody[] = $response->getRawBody();

		return ["httpStatus" => $getHTTPStatus, "rawBody" => $getRawBody];
	}

	public static function replyRequireRegister($replyToken)
	{
		$flexMessageBuider = new FlexMessageBuilder(null, null);
		$bubbleContainerBuilder = new BubbleContainerBuilder();
		$bodyComponentBuilder = new BoxComponentBuilder(null, null);
		$footerComponentBuilder = new BoxComponentBuilder(null, null);

		$bodyComponentBuilder->setLayout(ComponentLayout::VERTICAL);
		$bodyComponentBuilder->setContents([(new TextComponentBuilder(null))->setText("Press on this link to register, please!")->setWrap(true)]);

		$footerComponentBuilder->setLayout(ComponentLayout::VERTICAL);
		$footerComponentBuilder->setContents([(new ButtonComponentBuilder(null))->setAction(new UriTemplateActionBuilder("", ""))]);

		$bubbleContainerBuilder->setBody($bodyComponentBuilder);
		$bubbleContainerBuilder->setFooter($footerComponentBuilder);

		$flexMessageBuider->setAltText("Please register menber before use our services!");
		$flexMessageBuider->setContents($bubbleContainerBuilder);

		$resp = (new self)->bot->replyMessage($replyToken, $flexMessageBuider, "hi there");
	}

	public static function replyText($replyToken, $text)
	{
		(new self)->bot->replyText($replyToken, $text);
	}
}