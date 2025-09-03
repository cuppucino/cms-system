<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlainNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $subject, public string $body) {}

    public function build()
    {
        return $this->subject($this->subject)
            ->text('mail.plain')          // use a plain txt view
            ->with(['body' => $this->body]);
    }
}
