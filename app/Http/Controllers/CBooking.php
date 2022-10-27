<?php

namespace App\Http\Controllers;

use App\Models\MBooking;
use App\Models\MBookingHargaSatuan;
use App\Models\MBookingPropertiExtra;
use App\Models\MBookingExtra;
use App\Models\MBookingDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\Helper;
use DataTables;

class CBooking extends Controller
{
    use Helper;

    public function index()
    {
        return view('booking.index')            
            ->with('title','Booking');
    }
    public function detail($id)
    {        
        $data = MBooking::selectRaw('t_booking.*, m_properti.judul as judul, m_users.username as username, m_properti.id_tipe_booking as tipe_booking, m_status_booking.nama_status_booking as nama_status_booking')
            ->join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
            ->join('m_status_booking','t_booking.id_status_booking','m_status_booking.id_status_booking','left')
            ->join('m_users','t_booking.id_user','m_users.id_user','left')
            ->where('t_booking.id_booking',$id)->first();
        $harga_satuan = MBookingHargaSatuan::where('id_booking',$id)->get();
        $extra_service = MBookingPropertiExtra::where('id_booking',$id)->get();
        $extra = MBookingExtra::where('id_booking',$id)->get();
        $discount = MBookingDiscount::where('id_booking',$id)->get();
        return view('booking.detail')
            ->with('data',$data)
            ->with('harga_satuan',$harga_satuan)
            ->with('extra_service',$extra_service)
            ->with('extra',$extra)
            ->with('discount',$discount)
            ->with('id',$id)            
            ->with('title','Booking')
            ->with('titlePage','Detail');
    }
    public function confirm($id)
    {
        MBooking::where('id_booking',$id)->update(['id_status_booking' => 2]);
        return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
    }
    public function decline($id,Request $request)
    {
        MBooking::where('id_booking',$id)->update(['id_status_booking' => 3, 'alasan_reject' => $request->decline]);
        return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
    }
    public function extra($id,Request $request)
    {
        if ($request->nama_biaya_extra) {
            $total = 0;
            for ($i=0; $i < count($request->nama_biaya_extra); $i++) {
                $arr_extra[] = [                    
                    'id_booking' => $id,
                    'nama_biaya_extra' => $request->nama_biaya_extra[$i],
                    'harga' => $request->harga[$i],
                ];
                $total = $total + $request->harga[$i];
            }
            MBookingExtra::where('id_booking',$id)->delete();
            MBookingExtra::insert($arr_extra);
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service + $total - $tot->total_booking_discount;
            MBooking::where('id_booking',$id)->update(['total_booking_extra' => $total, 'harga_total' => $all]);
        }else {
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service + 0 - $tot->total_booking_discount;
            MBooking::where('id_booking',$id)->update(['total_booking_extra' => 0, 'harga_total' => $all]);
            MBookingExtra::where('id_booking',$id)->delete();
        }

        return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
    }
    public function discount($id,Request $request)
    {
        if ($request->nama_biaya_discount) {
            $total = 0;
            for ($i=0; $i < count($request->nama_biaya_discount); $i++) {
                $arr_discount[] = [                    
                    'id_booking' => $id,
                    'nama_biaya_discount' => $request->nama_biaya_discount[$i],
                    'harga' => $request->harga[$i],
                ];
                $total = $total + $request->harga[$i];
            }
            MBookingDiscount::where('id_booking',$id)->delete();
            MBookingDiscount::insert($arr_discount);
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service - $total + $tot->total_booking_extra;
            MBooking::where('id_booking',$id)->update(['total_booking_discount' => $total, 'harga_total' => $all]);
        }else {
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service - 0 + $tot->total_booking_extra;
            MBooking::where('id_booking',$id)->update(['harga_total' => $all, 'total_booking_discount' => 0]);
            MBookingDiscount::where('id_booking',$id)->delete();
        }

        return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
    }
    public function data()
    {        
        $model = MBooking::join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
            ->join('m_status_booking','t_booking.id_status_booking','m_status_booking.id_status_booking','left')
            ->selectRaw('t_booking.*, m_properti.judul as judul, m_properti.alamat as alamat, m_properti.id_tipe_booking as tipe_booking, m_properti.binatang as binatang, m_status_booking.nama_status_booking as nama_status_booking , m_status_booking.id_status_booking as id_status_booking')
            ->where('t_booking.deleted',1);
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                if ($row->tipe_booking == 1) {
                    $btn = '<a href="'.url('booking/detail/'.$row->id_booking).'" class="btn btn-success text-light">Confirm</a>';
                }else{
                    $btn = '<a href="'.url('booking/detail/'.$row->id_booking).'" class="btn btn-primary text-light">Detail</a>';
                }
                return $btn;
            })
            ->addColumn('status', function ($row) {
                // if ($row->id_status_booking == 1) {
                //     $btn = '<p class="bg-primary text-white" style="white-space: nowrap">'.$row->nama_status_booking.'</p>';
                // }elseif ($row->id_status_booking == 5 || $row->id_status_booking == 2) {
                //     $btn = '<p class="bg-success text-white" style="white-space: nowrap">'.$row->nama_status_booking.'</p>';
                // }else{
                //     $btn = '<p class="bg-danger text-white" style="white-space: nowrap">'.$row->nama_status_booking.'</p>';
                // }
                    $btn = '<p style="white-space: nowrap">'.$row->nama_status_booking.'</p>';
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
            ->rawColumns(['action','alamat','in','out','harga_total','status'])
            ->addIndexColumn()
            ->toJson();
    }    
}