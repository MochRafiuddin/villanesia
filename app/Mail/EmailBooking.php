<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailBooking extends Mailable
{
    use Queueable, SerializesModels;
public $nama_depan,$nama_belakang,$nama_properti,$tanggal_check_in,$view,$judul,$id_booking,$pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nama_depan,$nama_belakang,$nama_properti,$tanggal_check_in,$view,$judul,$id_booking,$pdf)
    {
        $this->nama_depan = $nama_depan;
        $this->nama_belakang = $nama_belakang;        
        $this->nama_properti = $nama_properti;
        $this->tanggal_check_in = $tanggal_check_in;
        $this->view = $view;
        $this->judul = $judul;
        $this->id_booking = $id_booking;        
        $this->pdf = $pdf;        
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
                    ->attachData($this->pdf,'invoice.pdf');
    }
}
