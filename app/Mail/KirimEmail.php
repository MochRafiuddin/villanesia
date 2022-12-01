<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KirimEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $username,$password,$judul;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username,$view,$judul)
    {
        $this->username = $username;
        $this->view = $view;
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
                    ->view($this->view);
    }
}
