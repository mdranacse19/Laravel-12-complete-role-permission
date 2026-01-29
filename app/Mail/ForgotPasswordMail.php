<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $email;
    public string $resetToken;
    public string $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, string $email, string $resetToken)
    {
        $this->userName = $userName;
        $this->email = $email;
        $this->resetToken = $resetToken;
        $this->resetUrl = route('api.v1.auth.reset-password') . "?token={$resetToken}&email={$email}";
    }

    /**
     * Build the message.
     */
    public function build(): ForgotPasswordMail
    {
        return $this->subject('Reset Your Password')
            ->view('emails.users.forgot_password')
            ->with([
                'userName' => $this->userName,
                'email' => $this->email,
                'resetToken' => $this->resetToken,
                'resetUrl' => $this->resetUrl,
                'frontendResetUrl' => config('app.url') . '/reset-password?token=' . $this->resetToken . '&email=' . $this->email,
            ]);
    }
}
