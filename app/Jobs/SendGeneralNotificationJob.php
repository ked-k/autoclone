<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\SendGeneralNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendGeneralNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
        protected $details;
        protected $user;
    
         public function __construct($details)
         {
            $this->details = $details;
            $this->user = User::where('id', $details['user_id'])->first();
         }
    
        /**
         * Execute the job.
         *
         * @return void
         */
        public function handle()
        {
            // foreach($this->procurementRequest->providers as $provider){
                $this->user->notify(new SendGeneralNotification($this->details));
                $user = User::where('id', 1)->first();
                // $user->notify(new SendGeneralNotification($this->details));
                // Clean up the generated PDF file
            // }
            
        }
}
