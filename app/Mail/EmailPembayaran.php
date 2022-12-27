<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailPembayaran extends Mailable
{
    use Queueable, SerializesModels;
    public $nama_depan,$nama_belakang,$view,$judul,$kode_booking,$no_telfon,$pdf,$email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nama_depan,$nama_belakang,$view,$judul,$kode_booking,$no_telfon,$pdf,$email)
    {
        $this->nama_depan = $nama_depan;
        $this->nama_belakang = $nama_belakang;        
        $this->view = $view;
        $this->judul = $judul;
        $this->kode_booking = $kode_booking;
        $this->no_telfon = $no_telfon;
        $this->pdf = $pdf;        
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->judul)
                    ->view($this->view)
                    ->attachData($this->pdf,'Trip-Detail.pdf');
    }
}
