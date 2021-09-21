<?php

namespace App\Jobs;

use App\Mail\CreateAccount;
use App\Mail\RemoveAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    protected $data;
    protected $to;
    protected $type;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $data, $type)
    {
        $this->data = $data;
        $this->to = $to;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch($this->type)
        {
            case 'create-account':
                Mail::to($this->to)->send(new CreateAccount($this->data['email'], $this->data['password']));
                break;
            case 'remove-account': 
                Mail::to($this->to)->send(new RemoveAccount());
                break;
            default: break;
        }
    }
}
