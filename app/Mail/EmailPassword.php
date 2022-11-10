<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $username,$password,$judul;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username,$password,$judul)
    {
        $this->username = $username;
        $this->password = $password;
        $this->judul = $judul;        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {          
        return $this->subject($this->judul)
                    ->view('email.mailView');
    }
}
