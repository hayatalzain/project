<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var mixed
     */
    protected $devices;
    /**
     * @var mixed
     */
    protected $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload,$devices)
    {
       // $this->type = $type;
        $this->devices = $devices;
        $this->payload = $payload;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       
     send_push($this->devices, $this->payload);
    // \Log::info('send notification new order');

    }
 



}