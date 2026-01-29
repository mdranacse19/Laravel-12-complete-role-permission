<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOtpSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $phoneNumber,
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
            // Placeholder for SMS sending via Twilio or other SMS provider
            // This can be implemented based on your SMS service provider
            
            $message = "Your OTP code is: {$this->otpCode}. Valid for {$this->expiryMinutes} minutes.";
            
            // Example: Twilio implementation
            // $twilio = new \Twilio\Rest\Client(
            //     config('kaz.otp.sms.twilio.account_sid'),
            //     config('kaz.otp.sms.twilio.auth_token')
            // );
            //
            // $twilio->messages->create(
            //     $this->phoneNumber,
            //     ['from' => config('kaz.otp.sms.twilio.phone_from')],
            //     ['body' => $message]
            // );
            
            \Log::info("OTP SMS would be sent to {$this->phoneNumber}: {$message}");
        } catch (\Exception $e) {
            \Log::error('OTP SMS sending failed for ' . $this->phoneNumber . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
