<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MBooking;
use App\Models\MProperti;
use App\Models\MBookingDiscount;
use App\Models\MBookingExtra;
use App\Models\MBookingHargaSatuan;
use App\Models\MBookingPropertiExtra;
use App\Models\MApiKey;
use App\Models\HReviewRating;
use App\Models\TKonfirmasiBayar;
use App\Traits\Helper;

class CABooking extends Controller
{
    use Helper;

    public function get_booking(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;
        $limit = 6;
        $page = ($request->page-1)*$limit;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $tipe = MBooking::selectRaw('t_booking.id_booking, t_booking.kode_booking, t_booking.id_ref, t_booking.id_user, t_booking.tanggal_mulai, t_booking.tanggal_selesai, t_booking.created_date, t_booking.id_status_booking, m_properti.id_bahasa, m_properti.id_ref_bahasa, m_properti.judul, m_properti.alamat, m_properti.harga_tampil, m_properti.jumlah_kamar_tidur, m_properti.jumlah_kamar_mandi, (m_properti.jumlah_tamu+COALESCE(m_properti.jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, m_properti.sarapan, m_properti.nilai_rating, m_properti.nama_file, m_status_booking.nama_status_booking')
                ->leftJoin('m_properti','m_properti.id_ref_bahasa','=','t_booking.id_ref')                
                ->leftJoin('m_status_booking','m_status_booking.id_ref_bahasa','=','t_booking.id_status_booking')                
                ->where('t_booking.deleted',1)
                ->where('t_booking.id_user',$user->id_user)
                ->where('m_properti.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->where('m_status_booking.id_bahasa',$id_bahasa)
                ->orderBy('t_booking.created_date','desc')
                ->limit($limit)
                ->offset($page)
                ->get();

        $total_page = 0;
        $get_total_all_data = MBooking::select('id_booking')
                            ->leftJoin('m_properti','m_properti.id_ref_bahasa','=','t_booking.id_ref')                
                            ->leftJoin('m_status_booking','m_status_booking.id_ref_bahasa','=','t_booking.id_status_booking')                
                            ->where('t_booking.deleted',1)
                            ->where('t_booking.id_user',$user->id_user)
                            ->where('m_properti.deleted',1)
                            ->where('m_properti.id_bahasa',$id_bahasa)
                            ->count();

        $hasil_bagi = $get_total_all_data / $limit;
        if(fmod($get_total_all_data, $limit) == 0){
            $total_page = $hasil_bagi;
        }else{
            $total_page = floor($hasil_bagi)+1;
        }
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'total_data' => count($tipe),
            'result' => $tipe,
            'total_page' => $total_page
        ], 200);        
    }

    public function get_booking_detail(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;
        $id_booking = $request->id_booking;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $detail_booking = MBooking::from( 't_booking as a' )
            ->selectRaw('a.*, b.id_bahasa, b.id_ref_bahasa, b.judul, b.alamat, b.harga_tampil, b.total_rating, b.nilai_rating, b.nama_file, b.kode_pos, b.kebijakan_pembatalan, b.merokok, b.binatang, b.acara, b.anak, b.aturan_tambahan, b.id_tipe_booking, c.nama_status_booking, CONCAT(e.nama_depan," ",e.nama_belakang) as nama_pemilik_properti, e.nama_foto as foto_pemilik_properti, d.email as email_pemilik_properti, d.no_telfon as no_telfon_pemilik_properti, CONCAT(g.nama_depan," ",g.nama_belakang) as nama_pemesan, h.nama_tipe_properti, i.nama_jenis_tempat')
            ->leftJoin('m_properti as b','a.id_ref', '=','b.id_ref_bahasa')
            ->leftJoin('m_status_booking as c','a.id_status_booking', '=','c.id_ref_bahasa')
            ->leftJoin('m_users as d','d.id_user', '=','b.created_by')
            ->leftJoin('m_customer as e','d.id_ref', '=','e.id')
            ->leftJoin('m_users as f','f.id_user', '=','a.id_user')
            ->leftJoin('m_customer as g','f.id_ref', '=','g.id')
            ->leftJoin('m_tipe_properti as h','h.id_ref_bahasa', '=','b.id_tipe_properti')
            ->leftJoin('m_jenis_tempat as i','i.id_ref_bahasa', '=','b.id_jenis_tempat')
            ->where('h.id_bahasa',$id_bahasa)
            ->where('i.id_bahasa',$id_bahasa)
            ->where('a.deleted',1)
            ->where('a.id_user',$user->id_user)
            ->where('b.deleted',1)
            ->where('b.id_bahasa',$id_bahasa)
            ->where('c.id_bahasa',$id_bahasa)
            ->where('a.id_booking',$id_booking)
            ->where('d.deleted',1)
            ->where('f.deleted',1)->get();
        
        $detail_booking_harga_satuan = MBookingHargaSatuan::where('id_booking',$id_booking)->get();
        // $detail_booking_properti_extra = MBookingPropertiExtra::where('id_booking',$id_booking)->get();
        $detail_booking_properti_extra = MBookingPropertiExtra::selectRaw('t_booking_properti_extra.*, m_properti_extra.nama_service as nama_properti_extra, m_properti_extra.tipe as tipe_properti_extra')->leftJoin('m_properti_extra','t_booking_properti_extra.id_properti_extra', '=','m_properti_extra.id_properti_extra')->where('t_booking_properti_extra.id_booking',$id_booking)->get();
        $detail_booking_extra = MBookingExtra::where('id_booking',$id_booking)->get();
        $detail_booking_discount = MBookingDiscount::where('id_booking',$id_booking)->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
            'detail_booking' => $detail_booking,
            'detail_payment' =>[
                'detail_booking_harga_satuan' => $detail_booking_harga_satuan,
                'detail_booking_properti_extra' => $detail_booking_properti_extra,
                'detail_booking_extra' => $detail_booking_extra,
                'detail_booking_discount' => $detail_booking_discount,
            ]
        ], 200);        
    }
    public function post_review(Request $request)
    {
        $id_booking = $request->id_booking;
        $id_properti = $request->id_properti;
        $rating = $request->rating;
        $review = $request->review;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $hrating = new HReviewRating();
        $hrating->id_properti = $id_properti;
        $hrating->id_booking = $id_booking;
        $hrating->id_user = $user->id_user;
        $hrating->review = $review;
        $hrating->rating = $rating;
        $hrating->save();

        $jumlah_rating = HReviewRating::where('id_properti',$id_properti)->where('id_booking',$id_booking)->get()->count();
        $total_rating = HReviewRating::where('id_properti',$id_properti)->where('id_booking',$id_booking)->get()->sum('rating');
        $rata = $total_rating / $jumlah_rating;
    
        $prop = MProperti::where('id_ref_bahasa',$id_properti)->update(['nilai_rating' => $rata, 'total_rating' => $jumlah_rating, 'total_review' => $jumlah_rating]);

        $prop = MBooking::where('id_booking',$id_booking)->update(['review'=>1]);

        return response()->json([
            'success' => true,
            'message' => 'Success Added Review',
            'code' => 1,            
        ], 200);        
    }
    public function post_booking_cancel(Request $request)
    {
        $id_bahasa = $request->id_bahasa;
        $id_booking = $request->id_booking;
        $alasan = $request->alasan;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();        
    
        $prop = MBooking::where('id_booking',$id_booking)->update(['id_status_booking' => 4, 'alasan_cancel' => $alasan]);

        return response()->json([
            'success' => true,
            'message' => 'booking cancel',
            'code' => 1,            
        ], 200);        
    }

    public function email_pembayaran(Request $request)
    {
        $email = $request->email;
        $nama_depan = $request->nama_depan;
        $nama_belakang = $request->nama_belakang;
        $username = $request->username;
        $password = $request->password;
        $nama_properti = $request->nama_properti;
        $tanggal_check_in = $request->tanggal_check_in;

        // $this->kirim_email($email,$nama_depan,$nama_belakang,'email.emailDaftar','Thank you for signing up with Villanesia');
        // $this->kirim_email($email,$nama_depan,$nama_belakang,$username,$password,null,null,'email.mailView','Forgot Password - Villanesia');
        // $this->kirim_email($email,$nama_depan,$nama_belakang,null,null,$nama_properti,$tanggal_check_in,'email.mailBooking','Availability Confirmation - ORDER ID #202211230001 - Villanesia');
        // $this->kirim_email($email,$nama_depan,$nama_belakang,null,null,null,null,'email.mailPembayaran','Proof of payment - ORDER ID #202211230001 - Villanesia');

        return response()->json([
            'success' => true,
            'message' => 'Kirim Email Success',
            'code' => 1,            
        ], 200);        
    }

    public function get_billing_address(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        
        $detail_t_konfirmasi_bayar = TKonfirmasiBayar::where('created_by',$user->id_user)->where('nama_depan','!=','')->orderBy('created_date','desc')->limit(1)->offset(0)->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
            'detail_billing_address' => $detail_t_konfirmasi_bayar
        ], 200);        
    }
}
