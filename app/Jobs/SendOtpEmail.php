<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $email,
        protected string $userName,
        protected string $otpCode,
        protected int $expiryMinutes
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new OtpMail($this->userName, $this->otpCode, $this->expiryMinutes));
        } catch (\Exception $e) {
            \Log::error('OTP email sending failed for ' . $this->email . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
