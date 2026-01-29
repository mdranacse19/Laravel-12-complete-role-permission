<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $otp,
        public int $expiryMinutes = 10
    ) {}

    public function build()
    {
        return $this->subject('Your OTP Code')
            ->view('emails.users.otp')
            ->with([
                'name' => $this->name,
                'otp' => $this->otp,
                'expiryMinutes' => $this->expiryMinutes,
            ]);
    }
}
