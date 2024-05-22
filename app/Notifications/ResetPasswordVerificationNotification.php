<?php

namespace App\Notifications;

use Ichtrojan\Otp\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    private $otp;

    public function __construct()
    {
        $this->message = 'Use the following code to reset your password';
        $this->subject = 'Password Reset';
        $this->fromEmail = 'no-reply@example.com';
        $this->otp = new otp;
        $this->mailer = "smtp";
    }
    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable): MailMessage
    {
        $otp = $this->otp->generate($notifiable->email, 'numeric', 6, 60);
        return (new MailMessage)
            ->mailer('smtp')
            ->subject($this->subject)
            ->greeting('Hello ' . $notifiable->name)
            ->line($this->message)
            ->line('code: ' . $otp->token);
    }



    public function toArray($notifiable)
    {
        return [];
    }
}
