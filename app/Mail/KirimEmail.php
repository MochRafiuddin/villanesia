<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KirimEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama_depan,$nama_belakang,$username,$password,$nama_properti,$tanggal_check_in,$view,$judul,$id_booking,$email,$no_telfon;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nama_depan,$nama_belakang,$username,$password,$nama_properti,$tanggal_check_in,$view,$judul,$id_booking,$email,$no_telfon)
    {
        $this->nama_depan = $nama_depan;
        $this->nama_belakang = $nama_belakang;
        $this->username = $username;
        $this->password = $password;
        $this->nama_properti = $nama_properti;
        $this->tanggal_check_in = $tanggal_check_in;
        $this->view = $view;
        $this->judul = $judul;
        $this->id_booking = $id_booking;
        $this->email = $email;
        $this->no_telfon = $no_telfon;
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
