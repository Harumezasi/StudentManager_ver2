<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class TempPasswordGenerated extends Mailable
{
    use Queueable, SerializesModels;
    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $argUser)
    {
        //
        $this->subject  = __('mail.password_title');
        $this->user     = $argUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'user'  => $this->user
        ];

        return $this->view('email.password', $data);
    }
}
