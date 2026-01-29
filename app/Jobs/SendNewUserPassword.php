<?php

namespace App\Jobs;

use App\Mail\NewUserPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewUserPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public string $plainPassword;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->user->email)) {
            return;
        }

        Mail::to($this->user->email)->send(new NewUserPasswordMail($this->user, $this->plainPassword));
    }
}

