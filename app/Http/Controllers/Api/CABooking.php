<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MBooking;
use App\Models\MApiKey;

class CABooking extends Controller
{
    public function get_booking(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();        ;

        $tipe = MBooking::selectRaw('t_booking.id_booking, t_booking.kode_booking, t_booking.id_ref, t_booking.id_user, t_booking.tanggal_mulai, t_booking.tanggal_selesai, t_booking.created_date, t_booking.id_status_booking, m_properti.id_bahasa, m_properti.id_ref_bahasa, m_properti.judul, m_properti.alamat, m_properti.harga_tampil, m_properti.jumlah_kamar_tidur, m_properti.jumlah_kamar_mandi, (m_properti.jumlah_tamu+COALESCE(m_properti.jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, m_properti.sarapan, m_properti.nilai_rating, m_status_booking.nama_status_booking')
                ->join('m_properti','m_properti.id_ref_bahasa','t_booking.id_ref')                
                ->join('m_status_booking','m_status_booking.id_ref_bahasa','t_booking.id_status_booking')                
                ->where('t_booking.deleted',1)
                ->where('t_booking.id_user',$user->id_user)
                ->where('m_properti.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->orderBy('t_booking.created_date','desc')
                ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'total_data' => count($tipe),
            'result' => $tipe,
        ], 200);        
    }
}
