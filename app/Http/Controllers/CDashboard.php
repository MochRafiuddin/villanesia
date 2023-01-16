<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MProperti;
use App\Models\MBooking;
use App\Models\MBahasa;
use App\Traits\Helper;
use DataTables;

class CDashboard extends Controller
{
    use Helper;

    public function index()
    {
        $bahasa = MBahasa::where('is_default',1)->first();
        $pro = MProperti::where('id_bahasa',$bahasa->id_bahasa)->where('deleted','!=',0)->get()->count();        
        $book = MBooking::where('deleted',1)->get()->count();
        return view('dashboard.index')
        ->with('pro',$pro)
        ->with('book',$book)
        ->with('title','Dashboard');
    }

    public function data_booking()
    {        
        $model = MBooking::join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
            ->join('m_status_booking','t_booking.id_status_booking','m_status_booking.id_ref_bahasa','left')
            ->selectRaw('t_booking.*, m_properti.judul as judul, m_properti.alamat as alamat, m_properti.id_tipe_booking as tipe_booking, m_properti.binatang as binatang, m_status_booking.nama_status_booking as nama_status_booking , m_status_booking.id_status_booking as id_status_booking')
            ->where('t_booking.deleted',1)
            ->where('m_status_booking.id_bahasa',1)
            ->where('t_booking.id_status_booking',1);
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                if ($row->id_status_booking == 1) {
                    $btn = '<a href="'.url('booking/detail/'.$row->id_booking).'" class="btn btn-success text-light">Confirm</a>';
                }else{
                    $btn = '<a href="'.url('booking/detail/'.$row->id_booking).'" class="btn btn-primary text-light">Detail</a>';
                }
                return $btn;
            })
            ->addColumn('status', function ($row) {
                if ($row->id_status_booking == 1) {
                    $btn = '<p class="badge badge-primary text-white">New</p>';
                }elseif ($row->id_status_booking == 5 || $row->id_status_booking == 2) {
                    $btn = '<p class="badge badge-success text-white">Waiting Payment</p>';
                }else{
                    $btn = '<p class="badge badge-danger text-white">'.$row->nama_status_booking.'</p>';
                }
                    // $btn = '<p style="white-space: nowrap">'.$row->nama_status_booking.'</p>';
                return $btn;
            })
            ->addColumn('date', function ($row) {                                
                $date = date('d-m-Y H:i', strtotime($row->created_date));
                return $date;
            })
            ->addColumn('alamat', function ($row) {                                
                $date = '<p>'.$row->judul.'</p><p style="white-space: nowrap">'.$row->alamat.'</p>';
                return $date;
            })
            ->addColumn('in', function ($row) {
                if ($row->tipe_booking == 5) {                    
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_mulai.' '.$row->jam_mulai)).' at '.date('H:i', strtotime($row->tanggal_mulai.' '.$row->jam_mulai)).'</p>';
                }else {
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_mulai)).'</p>';
                }
                return $date;
            })
            ->addColumn('out', function ($row) {
                if ($row->tipe_booking == 5) {                    
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_mulai.' '.$row->jam_selesai)).' at '.date('H:i', strtotime($row->tanggal_mulai.' '.$row->jam_selesai)).'</p>';
                }else {
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_selesai)).'</p>';
                }
                return $date;
            })
            ->addColumn('tamu', function ($row) {                
                $date = ($row->tamu_dewasa + $row->tamu_anak + $row->tamu_bayi);
                return $date;
            })
            ->addColumn('pet', function ($row) {                
                if ($row->binatang == 1) {
                    $data = "Yes";
                }else {
                    $data = "No";
                }
                return $data;
            })
            ->addColumn('harga_total', function ($row) {                
                $nominal = "<p>Rp.".$this->ribuan(ceil($row->harga_total))."</p>";
                return $nominal;
            })
            ->editColumn('kode_booking', function ($row) {                
                $nominal = "#".$row->kode_booking;
                return $nominal;
            })
            ->rawColumns(['action','alamat','in','out','harga_total','status'])
            ->addIndexColumn()
            ->toJson();
    }    
}
