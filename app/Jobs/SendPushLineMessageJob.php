<?php

namespace App\Jobs;

use App\Services\LineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushLineMessageJob implements ShouldQueue
{
    private $to;
    private $action;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $action = "create-account")
    {
        $this->$to = $to;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch($this->action)
		{
			case "create-account": 
                $response = LineService::pushMessageCreateAccountSuccess($this->to);
                Log::info("PUSH MESSAGE: ");
                Log::info("HTTP_STATUS: ".print_r($response["httpStatus"], true));
                Log::info("RAW_BODY: ".print_r($response["rawBody"], true));
				break;
		}
    }
}
